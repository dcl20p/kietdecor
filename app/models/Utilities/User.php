<?php

namespace Models\Utilities;

use Laminas\Math\Rand;
use Laminas\Mvc\Controller\AbstractActionController;
use Models\Entities\AccessLog;
use Models\Entities\Admin;
use Models\Utilities\FileHandler;

class User
{
    /**
     * Max Wrong count
     * @var integer
     */
    const MAX_WRONG_COUNT = 5;

    /**
     * Timeout of of wrong password.
     * @var integer
     */
    const MAX_WRONG_PASS_TIMEOUT = 18000; // 300 minutes

    /**
     * Validate request based on email, ip, and site.
     *
     * @param string $email Email to validate
     * @param string $ip IP address to validate
     * @param string $site Site to validate against
     *
     * @return bool True if the request is valid, false otherwise
     */
    public static function isValidRequest(string $email, string $ip = '', string $site = ''): bool
    {
        $configs = @include(DATA_PATH . "/system/{$site}_configs.php") ?: [];

        $emails = $configs['user']['items'] ?? [];
        $ips = $configs['ip_address']['items'] ?? [];

        // Is valid email
        if (array_key_exists($email, $emails)) {
            return true;
        }

        // Is valid address
        if (empty($ips) || array_key_exists($ip, $ips)) {
            return true;
        }

        $hours = $configs['work_hour']['items'] ?? [];
        $start = $hours['start'] ?? 0;
        $end   = $hours['end'] ?? 24;
        $hour  = (int)date('H'); 

        if ($hour >= $start && $hour <= $end) {
            return true;
        }

        return false;
    }

    /**
     * Create path to save log login
     * @param string $email
     * @param array $device
     * @return string
     */
    public static function getCheckLoginPath(string $email, array $device = []): string
    {
        $device['email'] = $email;
        $email = crc32((json_encode($device)));

        return implode(DIRECTORY_SEPARATOR, [
            DATA_PATH, 'log_login', APPLICATION_SITE, "login_{$email}.php"
        ]);
    }

    /**
     * Password plugin. Blocked account after login with wrong 
     * password more than 10 times.
     * @param string $email
     * @param array $device
     * @return boolean
     */
    public static function checkLoginRequest(string $email, array $device = []): bool
    {
        $filePath = self::getCheckLoginPath($email, $device);

        if (is_file($filePath)) {
            $data = require $filePath;

            if ($data['time'] >= time()) {
                $count = $data['count'] + 1;
                if ($count < self::MAX_WRONG_COUNT) {
                    $data['count'] = $count;
                    FileHandler::writePhpArray($filePath, $data);
                    return true;
                } else {
                    return false;
                }
            }
        }

        FileHandler::writePhpArray($filePath, [
            'count' => 1,
            'time' => time() + self::MAX_WRONG_PASS_TIMEOUT
        ]);

        return true;
    }

    /**
     * Log access site
     * @var array
     */
    const ACCESS_SITES = [
        'manager' => AccessLog::SITE_MANAGERS,
        'customer'=> AccessLog::SITE_CUSTOMER,
    ];

    /**
     * Save log access of user
     *
     * @param AbstractActionController $ctrl
     * @param string $idKey
     * @param string $codeKey
     * @param boolean $saveRedis
     * @return mixed
     */
    public static function saveLogAccess(
        AbstractActionController $ctrl, 
        $idKey = '', 
        $codeKey = '',
        $saveRedis = true): void
    {
        $authen = $ctrl->getAuthen();
        $preventRoutes = ['un-scribe-firebase' => 1, 'sub-scribe-firebase'=> 1];
        if ($authen && $authen->{$idKey}
            && empty($preventRoutes[
                $routeName = $ctrl->getCurrentRouteName()
            ])
        ) {
            $time = time();
            try {
                $repo = $ctrl->getEntityRepo(AccessLog::class);
                $repo->insertData($logData = [
                    'al_user_id'       => $authen->{$idKey},
                    'al_user_code'     => $authen->{$codeKey},
                    'al_url'           => $ctrl->getRequest()->getUri()->getPath(),
                    'al_method'        => strtoupper($ctrl->getRequest()->getMethod()),
                    'al_route_name'    => $routeName,
                    'al_params'        => [
                        'get'   => $ctrl->getParamsQuery(),
                        'post'  => $ctrl->getParamsPost(),
                        'files' => $ctrl->getParamsFiles(),
                        'route' => $ctrl->getParamsRoute()
                    ],
                    'al_created'       => $time,
                    'al_site'          => self::ACCESS_SITES[APPLICATION_SITE] ?? '',
                    'al_ip_address'    => $ctrl->getZfHelper()->getClientIp()
                ]);

                // Disabled save redis cache
                if (empty($saveRedis)) return ;
            
                $repo->putLogToRedis([
                    'session_id' => session_id(),
                    'access'     => $logData
                ]);
                
                unset($logData,$repo);
            } catch (\Throwable $e) {
                $ctrl->saveErrorLog($e);
            }
        }
    }

    /**
     * Save log login of user
     *
     * @param AbstractActionController $ctrl
     * @param string $email
     * @param array $options
     * @return void
     */
    public static function saveLogLogin(
        AbstractActionController $ctrl,
        string $email,
        array $options = []
    ): void 
    {
        $time = time();
        try {
            $user = $ctrl->getEntityRepo($options['class'])
                ->findOneBy([$options['email_key'] => $email]);
            
            $postData = $ctrl->getParamsPost();
            if (!empty($postData['password'])) {
                $postData['password'] = str_repeat(
                    '*', strlen($postData['password'])
                );
            }

            $matches = [];
            preg_match('/(.*)\@(.*)/im', $email, $matches);
            $postData['email'] = "{$matches[1]}@" . str_repeat('*', strlen($matches[2]));

            $ctrl->getEntityRepo(AccessLog::class)->insertData([
                'al_user_id'       => $user->{$options['id_key']} ?? null,
                'al_user_code'     => $user->{$options['code_key']} ?? '',
                'al_url'           => $ctrl->getRequest()->getUri()->getPath(),
                'al_method'        => strtoupper($ctrl->getRequest()->getMethod()),
                'al_route_name'    => $ctrl->getCurrentRouteName(),
                'al_params'        => [
                    'get'   => $ctrl->getParamsQuery(),
                    'post'  => $postData,
                    'files' => $ctrl->getParamsFiles(),
                    'route' => $ctrl->getParamsRoute()
                ],
                'al_created'       => $time,
                'al_site'          => self::ACCESS_SITES[APPLICATION_SITE] ?? '',
                'al_ip_address'    => $ctrl->getZfHelper()->getClientIp(),
                'al_state'         => $options['state'] ?? '',
                'al_content'       => $options['content'] ?? ''
            ]);

        } catch (\Throwable $e) {
            $ctrl->saveErrorLog($e);
        }
    }

    /**
     * Salt length
     * @var integer
     */
    const SALT_KEY = 9;

    /**
     * Decode string
     *
     * @param string $str
     * @return string
     */
    public static function decodeStr(string $str = ''): string
    {
        if (empty($str)) return '';
        return gzuncompress(base64_decode(substr($str, self::SALT_KEY)));
    }

    /**
     * Encode string
     *
     * @param string $str
     * @return string
     */
    public static function encodeStr(string $str = ''): string
    {
        if (empty($str)) return '';

        return Rand::getString(
            self::SALT_KEY,
            'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            true
        ) . base64_encode(gzcompress($str));
    }

    /**
     * Get avatar of user admin
     *
     * @param object $user
     * @return string
     */
    public static function getAvatarUser($user): string
    {
        if (empty($user)) return '';
        $imgNameDefault = ($user->adm_gender ?? Admin::GENDER_MALE) == Admin::GENDER_FEMALE
            ? 'user-default-female.png'
            : 'user-default_male.png';

        if ($user->adm_avatar) {
            $imgPath = Admin::getImgPath();
            $srcImg = str_replace('__id__', $user->adm_code, $imgPath)
                . $user->adm_avatar;
            if (!file_exists(PUBLIC_PATH.$srcImg)) {
                $srcImg = "/assets/manager/images/users/{$imgNameDefault}";
            }
        } else {
            $srcImg = "/assets/manager/images/users/{$imgNameDefault}";
        }

        return $srcImg;
    }
}
