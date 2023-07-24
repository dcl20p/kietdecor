<?php

namespace Manager\Service;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Models\Entities\Admin;
use Models\Entities\Client;
use Models\Entities\Device;
use Models\Entities\Session;
use Models\Utilities\User;
use Ramsey\Uuid\Uuid;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns its identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * User email.
     * @var string 
     */
    private string $email;

    /**
     * Password
     * @var string 
     */
    private string $password;

    /**
     * Ip address
     * @var string
     */
    private string $ipAddress;

    /**
     * Device
     * @var array
     */
    protected array $device = [];

    /**
     * Current user
     * @var \Models\Entities\Admin
     */
    private $user = null;

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;

    const INVALID_IP_ADDRESS = -1000;

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Set the user's password address.
     *
     * @param string $password The password address to set.
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Set the user's email address.
     *
     * @param string $email The email address to set.
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Set the user's ip address.
     *
     * @param string $ipAddress The ip address to set.
     * @return void
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Set the user's device.
     *
     * @param array $device The device to set.
     * @return void
     */
    public function setDevice(array $device = []): void
    {
        $this->device = $device;
    }

    /**
     * Get user from database
     * @return \Models\Entities\Admin
     */
    protected function getUser()
    {
        if (empty($this->user))
            $this->user = $this->entityManager->getRepository(Admin::class)
                ->findOneBy(['adm_email' => $this->email]);
        return $this->user;
    }

    /**
     * Create object 
     *
     * @param array $options
     * @return object
     */
    public function getResultRowObject(array $options = []): object
    {
        $user = $this->getUser();
        $object = new \stdClass();

        foreach($options as $column) {
            $object->{$column} = $user->{$column}; 
        }

        return $object;
    }

    /**
     * Delete client token
     *
     * @param integer $uId
     * @return void
     */
    public function clearPushToken(int $uId): void
    {
        $this->entityManager->getRepository(Client::class)
            ->unScriptClient([
                'user_id'   => $uId,
                'area_type' => Client::AREA_MANAGER
            ]);
    }
    /**
     * Save active time
     *
     * @param array $options
     * @return boolean
     */
    public function saveLogActive(array $options = []): bool
    {
        $repo = $this->entityManager->getRepository(Admin::class);
        $user = $this->getUser();

        $this->user = $user = $repo->updateData($user, [
            'adm_last_login_time' => $time = time(),
            'adm_last_access_time'=> $time,
            'adm_ssid'            => ($newSSID = session_id()),
            'adm_blocked_time'    => 0,
            'adm_first_login_time'=> ($user->adm_first_login_time > 0)
                ? $user->adm_first_login_time 
                : time()
        ]);

        // $oldSSID = $user->adm_ssid;
        // if ($oldSSID && $newSSID != $oldSSID) {
        //     $repo->delSessionData($oldSSID);
        // }
        // $this->clearPushToken($user->adm_id);

        // Save session login & device information
        $this->saveDeviceInfo($newSSID, $user);
        return true;
    }

    /**
     * Save session login & device information
     *
     * @param string $sessionId
     * @param Admin $user
     * @return boolean
     */
    protected function saveDeviceInfo(string $sessionId, Admin $user): bool
    {
        $repo = $this->entityManager->getRepository(Session::class);
        $repo->createDeviceInfo([
            'id' => $sessionId,
            'user_id' => $user->adm_id,
            'user_code' => $user->adm_code ?? '',
            'area_type' => Session::AREA_MANAGER,
            'agent' => $this->device['agent'] ?? '',
            'ip' => $this->device['ip_address'] ?? '',
            'provider' => $this->device['hostname'] ?? '',
            'browser' => $this->device['browser'] ?? '',
            'browser_ver' => $this->device['version'] ?? '',
            'device' => $this->device['device'] ?? '',
            'device_type' => $this->device['device_type'] ?? '',
            'type' => $this->device['type'] ?? '',
            'os' => $this->device['os'] ?? '',
            'os_ver' => $this->device['os_version'] ?? '',
            'sr_info' => @json_encode($this->device['screen']),
            'is_bot' => $this->device['device'] == 'BOT' ? 1 : 0,
            'time' => time(),
            'is_login' => 1
        ]);

        return true;
    }

    /**
     * Update state login of user
     *
     * @param integer $userId
     * @param boolean $state
     * @return boolean
     */
    public function updateStateLogin(int $userId, int $state): bool
    {
        $repo = $this->entityManager->getRepository(Session::class);
        $sessions = $repo->findOneBy([
            'ss_id'        => session_id(),
            'ss_user_id'   => $userId,
            'ss_area_type' => Session::AREA_MANAGER
        ]);
        if (!empty($sessions)) {
            $repo->updateData($sessions, ['ss_is_login' => $state]);
            return true;
        } else return false;
    }

    /**
     * Error code blocked login account
     * @var string
     */
    const ERROR_CODE_BLOCKED_ACC = 2856585320;

    /**
     * Message when account has been blocked
     * @var string
     */
    const LOCKED_ACC_MSG = 'Tài khoản của bạn đã bị khóa. Bạn đang cố đăng nhập sai mật khẩu hơn 5 lần.';

    /**
     * Check and auto locked login account
     * @param Admin $user
     * @return \Laminas\Authentication\Result
     */
    protected function checkAndLogUser(Admin $user)
    {
        if (false == User::checkLoginRequest(
            $user->adm_email,
            $this->device
        )) {
            $this->entityManager->getRepository(Admin::class)
                ->lockedStatus($user->adm_id, time());

            @unlink(User::getCheckLoginPath(
                $user->adm_email,
                $this->device
            ));

            return new Result(
                self::ERROR_CODE_BLOCKED_ACC,
                null,
                [self::LOCKED_ACC_MSG]
            );
        }
    }

    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {
        $user = $this->getUser();

        if (empty($user)) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Thông tin không hợp lệ.']
            );
        }
        $validGroup = [
            Admin::GROUP_SUPPORT => 1,
            Admin::GROUP_MANAGER => 2,
        ];

        // Check for case not manager & supporter
        if (
            empty($validGroup[$user->adm_groupcode])
            && !User::isValidRequest(
                $this->email,
                $this->ipAddress ?? '',
                APPLICATION_SITE
            )
        ) {
            return new Result(
                self::INVALID_IP_ADDRESS,
                null,
                ['Địa chỉ ip đăng nhập không hợp lệ.']
            );
        }

        // Check user unblock then 1 hour
        // If the user with such email exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->adm_status == Admin::STATUS_UNACTIVE) {
            if (
                $user->adm_blocked_time < 1
                || (time() - ($user->adm_blocked_time ?? 0))
                < User::MAX_WRONG_PASS_TIMEOUT
            ) {
                if (!empty($checkRs = $this->checkAndLogUser($user))) {
                    return $checkRs;
                }
                return new Result(
                    Result::FAILURE,
                    null,
                    ['Tài khoản đã bị tạm ngưng hoạt động.']
                );
            } else {
                $this->entityManager->getRepository(Admin::class)
                    ->openLockedStatus($user->adm_id);
            }
        }

        // Now we need to calculate hash based on user-entered password and compare
        // it with the password hash stored in database.
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getAdm_password(true);

        if ($bcrypt->verify($this->password, $passwordHash)) {
            @unlink(User::getCheckLoginPath(
                $user->adm_email,
                $this->device
            ));

            // Great! The password hash matches. Return user identity (email) to be
            // saved in session for later use.
            return new Result(
                Result::SUCCESS,
                ['id' => $user->adm_id, 'email' => $this->email, 'group' => $user->adm_groupcode],
                ['Xác thực thành công.']
            );
        }

        // Auto block account
        if (!empty($checkRs = $this->checkAndLogUser($user))) {
            return $checkRs;
        }

        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Thông tin không hợp lệ.']
        );
    }
}
