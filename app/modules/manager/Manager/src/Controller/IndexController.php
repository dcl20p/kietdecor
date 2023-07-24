<?php

declare(strict_types=1);

namespace Manager\Controller;

use Laminas\Authentication\Result;
use Laminas\Math\Rand;
use Laminas\Uri\Uri;
use libphonenumber\CountryCodeToRegionCodeMapForTesting;
use libphonenumber\PhoneNumberUtil;
use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Manager\Form\LoginForm;
use Models\Entities\Admin;
use Models\Utilities\{AppUtilities, User};
use Laminas\Http\PhpEnvironment\Response;

class IndexController extends ZfController
{
    /**
     * Auth manager.
     * @var \Manager\Service\AuthManager
     */
    private $authManager;

    /**
     * Auth service.
     * @var \Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * User manager.
     * @var Manager\Service\AdminManager
     */
    private $adminManager;

    /**
     * Constructor.
     */
    public function __construct($authManager, $authService, $adminManager)
    {
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->adminManager = $adminManager;
    }

    /**
     * Authenticates user given email address and password credentials.
     */
    public function loginAction()
    {
        try {
            $authen = $this->authService->getIdentity();

            if ($authen && $authen->adm_id != null) {
                return $this->redirectToRoute('home');
            }

            $redirectUrl = urldecode(
                (string) $this->getParamsQuery('redirectUrl', '')
            );

            if (strlen($redirectUrl) > 2048) {
                throw new \Exception($this->mvcTranslate("Tham số redirectUrl quá dài"));
            }

            // Create login form
            $form = new LoginForm('login-form', [
                'translator' => $this->mvcTranslate(),
                'route' => $this->getCurrentRouteName()
            ]);
            $form->get('redirect_url')->setValue($redirectUrl);

            // Store login status.
            $isLoginError = $isPost = false;

            if ($this->isPostRequest()) {
                $data = $this->getParamsPost();

                $isPost = true;
                $form->setData($data);
                if ($form->isValid()) { 
                    $data = $form->getData();
                    $email = strtolower(trim($data['email'] ?? ''));

                    $screen = @json_decode(
                        (string) $this->getParamsPost('screen', '{}'),
                        true
                    );

                    $device = $this->getDevice();
                    $device['screen'] = (array) $screen;

                    $result = $this->authManager->login(
                        $email, $data['password'],
                        (int) $data['remember_me'],
                        $this->getZfHelper()->getClientIp() ?? '',
                        $device
                    );

                    if ($result->getCode() === Result::SUCCESS) {
                        $this->saveLogLogin($email, 'YES', 'Login success');

                        $redirectUrl = $this->getParamsPost('redirect_url', '');

                        if (!empty($redirectUrl)) {
                            $uri = new Uri($redirectUrl);
                            if (!$uri->isValid() || $uri->getHost() != null) {
                                $msg = str_replace(
                                    '__uri__',
                                    $redirectUrl,
                                    $this->mvcTranslate('Incorrect redirect URL: __uri__')
                                );
                                throw new \Exception($msg);
                            }
                            return $this->redirectToUrl($redirectUrl);
                        } else {
                            return $this->redirectToRoute('home');
                        }
                    } else if ($result->getCode() === \Manager\Service\AuthAdapter::INVALID_IP_ADDRESS) {
                        $this->saveLogLogin($email, 'NO', 'Login with invalid ip address');

                        return $this->redirectToRoute('login-deny');
                    } else {
                        $this->addErrorMessage(
                            $this->mvcTranslate($result->getMessages()[0])
                        );
                    }
                } else {
                    $isLoginError = true;
                }

                if (
                    !empty($email = strtolower(trim($data['email'] ?? '')))
                    && filter_var($email, FILTER_VALIDATE_EMAIL)
                ) {
                    $this->saveLogLogin(
                        $email,
                        'NO',
                        'Intenal server error occurred'
                    );
                }
            }

        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            $this->saveErrorLog($e);
            $isLoginError = true;
            $form = new LoginForm('login-form', [
                'translator' => $this->mvcTranslate(),
                'route' => $this->getCurrentRouteName()
            ]);
        }
        $this->layout('layout/login');

        if ($isLoginError) {
            $this->addErrorMessage(
                $this->mvcTranslate('Email hoặc mật khẩu không chính xác')
            );
        }
        return new ViewModel([
            'form' => $form,
            'isPost' => $isPost,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl,
            'routeName' => $this->getCurrentRouteName()
        ]);
    }

    /**
     * The "logout" action performs logout operation.
     *
     * @return void
     */
    public function logoutAction(): Response
    {
        try {
            if (!empty($admId = $this->getLoginId())) {
                if ($this->getEntityRepo(Admin::class)->logout($admId)) {
                    $this->authManager->logout();
                }
            }
            return $this->redirectToRoute('login');
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            $this->saveErrorLog($e);

            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
            return $this->redirectToRoute('home');
        }
    }

    /**
     * Save login 
     * @param string $email
     * @param string $state
     * @param string $msg
     * @return void
     */
    protected function saveLogLogin($email, $state = '', $msg = ''): void
    {
        User::saveLogLogin(
            $this, $email, [
                'state'    => $state,
                'content'  => $msg,
                'class'    => Admin::class,
                'id_key'   => 'adm_id',
                'code_key' => 'adm_code',
                'email_key'=> 'adm_email'
            ]
        );
    }

    /**
     * This action displays the "Reset Password" page.
     * @deprecatedPermission: This action need granted to all user
     * @return JsonModel
     */
    public function resetPasswordAction(): JsonModel
    {
        $tokenFolder = 'reset_pw';

        if (
            false == $this->isValidDomain()
            || false == $this->isPostRequest()
            || false == $this->isValidCsrfToken(null, $tokenFolder)
        ) {
            return new JsonModel([
                'success' => false,
                'token' => $this->generateTokenPassword(),
                'msg' => $this->mvcTranslate(ZF_MSG_WENT_WRONG),
            ]);
        }

        $email = $this->validEmail(
            $this->getParamsPayload('email', '')
        );

        if (strlen($email) > 0) {
            $this->clearCsrfToken(null, $tokenFolder);

            $repo = $this->getEntityRepo(Admin::class);
            $user = $repo->findOneBy(['adm_email' => $email]);

            if (!empty($user)) {
                if ($user->adm_status == 0) {
                    if (
                        $user->adm_blocked_time < 1
                        || (time() - $user->adm_blocked_time ?? 0) < 3600
                    ) {
                        $user = null;
                        return new JsonModel([
                            'success' => false,
                            'token' => $this->generateCsrfToken(
                                [Admin::USER_FOLDER_TOKEN, microtime(true), rand(100, 999999)],
                                Admin::USER_FOLDER_TOKEN
                            ),
                            'msg' => $this->mvcTranslate('Tài khoản đã bị tạm ngưng hoạt động.'),
                        ]);

                    } else {
                        $this->getEntityRepo(Admin::class)
                            ->openLockedStatus($user->adm_id);
                    }
                }

                $result = true;
                try {
                    $this->startTransaction();
                    $password = $this->createRandomPassword();

                    $user->adm_password = $password;
                    $res = $repo->updateData($user, []);

                    // Send mail
                    if ($res) {
                        $result = $this->sendMailResetPassword($user, $password);
                    }

                    $this->commitTransaction();

                } catch (\Throwable $e) {
                    $this->rollbackTransaction();
                    $this->saveErrorLog($e);
                    $result = false;
                }

                return new JsonModel([
                    'success' => $result,
                    'token' => $this->generateTokenPassword(),
                    'msg' => $result
                    ? $this->mvcTranslate('Đổi mật khẩu thành công, mật khẩu đã được gửi vào email đăng ký của bạn.')
                    : $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
                ]);
            } else {
                return new JsonModel([
                    'success' => false,
                    'token' => $this->generateTokenPassword(),
                    'msg' => $this->mvcTranslate('Tài khoản không tồn tại.'),
                ]);
            }

        } else {
            return new JsonModel([
                'success' => false,
                'token' => $this->generateCsrfToken(
                    [Admin::USER_FOLDER_TOKEN, microtime(true), rand(100, 999999)],
                    Admin::USER_FOLDER_TOKEN
                ),
                'msg' => $this->mvcTranslate(ZF_MSG_UPDATE_FAIL),
            ]);
        }
    }

    /**
     * Generate csrf token for password
     * @return string
     */
    protected function generateTokenPassword(): string
    {
        return $this->generateCsrfToken(
            [Admin::USER_FOLDER_TOKEN, microtime(true), rand(100, 999999)],
            Admin::USER_FOLDER_TOKEN
        );
    }

    /**
     * Make random password
     * @return string
     */
    protected function createRandomPassword(): string
    {
        $pass = str_split(
            Rand::getString(2, 'abcdefghijklmnopqrstuvwxyz')
            . Rand::getString(2, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')
            . Rand::getString(2, '1234567890')
            . Rand::getString(2, '!@#$%&*-_+;,')
        );
        shuffle($pass);
        return implode('', $pass);
    }

    /**
     * Send mail notify reset password
     * @param \Model\Entities\Admin $user
     * @param string $password
     * @return bool
     */
    protected function sendMailResetPassword($user, $password): bool
    {
        return AppUtilities::sendMailByTmplCode(
            'ad_tmpl_send_mail_reset_pw',
            $this->getEntityManager(),
            ['{{fullname}}', '{{domain}}', '{{email}}', '{{password}}'],
            [$user->adm_fullname, DOMAIN_NAME, $user->adm_email, $password],
            ['email' => $user->adm_email, 'name' => $user->adm_fullname]
        );
    }

    /**
     * Check email valid
     * @param string $email
     * @return string
     */
    protected function validEmail(string $email = ''): string
    {
        if (strlen($email) === 0)
            return '';

        $specialChar = preg_quote('@_-.', '/');
        $email = preg_replace('/[^a-z0-9' . $specialChar . ']/i', '', $email);

        if (filter_var($email, FILTER_VALIDATE_EMAIL))
            return $email;

        return '';
    }

    /**
     * Validate the domain name against the HTTP Origin or Referer 
     * header and set Access-Control-Allow-Origin header.
     *
     * @return bool Returns true if the domain name is valid and 
     * Access-Control-Allow-Origin header is set, otherwise false.
     */
    protected function isValidDomain(): bool
    {
        try {
            $checkDomain = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
            if (!is_string($checkDomain)) {
                return false;
            }

            $isValidDomain = $this->matchDomain($checkDomain);
            if ($isValidDomain) {
                header('Access-Control-Allow-Origin: ' . $checkDomain);
                return true;
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return false;
    }

    /**
     * Match domain
     *
     * @param string $domain
     * @return boolean
     */
    protected function matchDomain(string $domain = ''): bool
    {
        if (empty($domain) || !is_string($domain)) {
            return false;
        }

        $url = parse_url($domain);
        $domain = ltrim($url['host'] ?? $domain, 'www');
        return (bool) preg_match('/^(' . preg_quote(DOMAIN_NAME, '/') . ')$/i', $domain);
    }

    /**
     * Login deny action
     *
     * @return ViewModel
     */
    public function loginDenyAction(): ViewModel
    {
        $view = new ViewModel();
        $view->setTemplate('manager/error/login-deny.phtml');
        return $view;
    }
}