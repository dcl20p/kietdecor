<?php

declare(strict_types=1);

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

try {
// var_dump("<pre>", ini_get('memory_limit'));die;
// phpinfo();die;
    error_reporting(E_ALL & ~E_NOTICE);
    // -- Turn off all error reporting
    //error_reporting(0);

    ini_set('default_charset', 'utf-8');

    // Time zone
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    /**
     * This makes our life easier when dealing with paths. Everything is relative
     * to the application root now.
     */
    $dirName = dirname(__FILE__);
    chdir($dirName);

    defined('LIBRARY_PATH') || define('LIBRARY_PATH', realpath($dirName . '/../../laminas_mvc/vendor'));
    // Composer autoloading
    include LIBRARY_PATH . '/autoload.php';
    
    // Handle file not found
    $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (!$uriPath) {
        header('HTTP/1.0 404 Not Found', true, 404);
        die();
    }
    if (preg_match('/.*(\.(ico|gif|jpe?g|png|bmp|jpg|pdf))$/', $uriPath)) {
        if (!realpath($dirName . $uriPath)) {
            header('HTTP/1.0 404 Not Found', true, 404);
            die();
        }
    }

    defined('APP_HTTPS') || define('APP_HTTPS', $_SERVER['HTTPS'] ?? '');
    defined('APP_SCRIPT_NAME') || define('APP_SCRIPT_NAME', $_SERVER['SCRIPT_NAME'] ?? '');
    defined('APP_SERVER_NAME') || define('APP_SERVER_NAME', $_SERVER['SERVER_NAME'] ?? '');
    defined('APP_REQUEST_TIME') || define('APP_REQUEST_TIME', $_SERVER['REQUEST_TIME'] ?? '');
    defined('APP_REQUEST_URI') || define('APP_REQUEST_URI', $_SERVER['REQUEST_URI'] ?? '');
    defined('APP_HTTP_ORIGIN') || define('APP_HTTP_ORIGIN', $_SERVER['HTTP_ORIGIN'] ?? '');
    defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath($dirName . '/../app/modules'));
    defined('DATA_PATH') || define('DATA_PATH', realpath($dirName . '/../data'));
    defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath($dirName));
    defined('CONFIG_PATH') || define('CONFIG_PATH', realpath($dirName . '/../configs'));
    defined('DOMAIN_NAME') || define('DOMAIN_NAME', str_replace('www.', '', APP_SERVER_NAME));
    // Setting sites
    $baseSite = explode('/', $trimedUri = str_replace('/en/', '/', APP_REQUEST_URI), 3);

    // $baseUrl = empty($baseSite[1]) || $baseSite[1] == 'en' ? '/' : "/{$baseSite[1]}";
    $baseUrl = empty($baseSite[1]) ? '/' : "/{$baseSite[1]}";

    $configSites = require_once CONFIG_PATH . '/sites-config.php';

    if ($trimedUri != APP_REQUEST_URI)
        $baseUrl = '/en';

    foreach ($configSites as $config) {

        list($domains, $users) = $config;
        if (in_array(DOMAIN_NAME, $domains)) {
            if (!isset($users[$baseUrl]))
            $baseUrl = '/';
            
            $tructure = $users[$baseUrl];

            // Define site
            defined('APPLICATION_SITE') || define('APPLICATION_SITE', $tructure['site']);
            defined('CSRF_TOKEN_DIR') || define('CSRF_TOKEN_DIR', $tructure['csrf_token_dir'] ?? $tructure['site']);
            //define ( 'APP_ENV_VERSION', 'release' );
            defined('APP_ENV_VERSION') || define('APP_ENV_VERSION', 'vtest');
            defined('APPLICATION_VERSION') || define('APPLICATION_VERSION', 'v1');
            defined('SESSION_FOLDER') || define('SESSION_FOLDER', 'session');
            // Define base url
            defined('BASE_URL') || define('BASE_URL', $baseUrl);

            // Define 
            defined('FOLDER_UPLOAD_BY_SITE') || define('FOLDER_UPLOAD_BY_SITE', $tructure['upload-folder']);

            // Dinh nghia ngon ngu
            defined('APPLICATION_LANGUAGE') || define('APPLICATION_LANGUAGE', $tructure['language'] ?? 'vi');

            // -- Dinh nghia locale
            defined('APPLICATION_LOCALE') || define('APPLICATION_LOCALE', $tructure['locale'] ?? 'vi_VN');

            $skinName = $tructure['skin-name'] ?? 'assets';
            defined('APPLICATION_SKIN_NAME') || define('APPLICATION_SKIN_NAME', $skinName);

            defined('APPLICATION_DATE') || define('APPLICATION_DATE', $tructure['date']['date'] ?? 'Y/n/j');
            defined('APPLICATION_DATE_TIME') || define('APPLICATION_DATE_TIME', $tructure['date']['date-time'] ?? 'Y/n/j G:i');
            defined('APPLICATION_DATE_MONTH') || define('APPLICATION_DATE_MONTH', $tructure['date']['month'] ?? 'n/j');
            defined('APPLICATION_MONTH') || define('APPLICATION_MONTH', $tructure['date']['monthShort'] ?? 'Y/n');
            defined('APPLICATION_HOUR') || define('APPLICATION_HOUR', $tructure['date']['hour'] ?? 'G:i');
            defined('APPLICATION_DATE_STR') || define('APPLICATION_DATE_STR', $tructure['date']['date-str'] ?? 'Y年n月j日');
            defined('APP_MYSQL_DATE') || define('APP_MYSQL_DATE', $tructure['date']['mysqlDate'] ?? '%d/%m/%Y');
            defined('APP_MYSQL_DATE_TIME') || define('APP_MYSQL_DATE_TIME', $tructure['date']['mysqlDateTime'] ?? '%Y/%m/%d %H:%i');
            defined('APP_JS_DATE') || define('APP_JS_DATE', $tructure['date']['jsDate'] ?? 'YYYY/M/D');
            defined('APP_JS_DATE_TIME') || define('APP_JS_DATE_TIME', $tructure['date']['jsDateTime'] ?? 'YYYY/M/D H:mm');
            defined('APP_JS_DATE_IN_WEEK') || define('APP_JS_DATE_IN_WEEK', $tructure['date']['jsDateInWeek'] ?? 'M月D日（dddd）');
            defined('APP_JS_TIME_DATE') || define('APP_JS_TIME_DATE', $tructure['date']['jsTimeDate'] ?? 'MM/D H:mm');
            defined('APP_JS_MONTH') || define('APP_JS_MONTH', $tructure['date']['jsMonth'] ?? 'YYYY/M');
            defined('APP_CHAT_TIME') || define('APP_CHAT_TIME', $tructure['date']['chatTime'] ?? 'n/j G:i');
            defined('APP_JS_TIME') || define('APP_JS_TIME', $tructure['date']['jsTime'] ?? 'H:mm');
            // Break
            break;
        }
    }

    // Case: Site not found
    if (!defined('APPLICATION_SITE')) {
        session_write_close();
        header('HTTP/1.0 404 Not Found', true, 404);
        exit();
    }

    // Define cookie domain
    defined('COOKIE_DOMAIN') || define('COOKIE_DOMAIN', DOMAIN_NAME);
    defined('SESSION_PREFIX') || define('SESSION_PREFIX', 'sess_');

    include APPLICATION_PATH . '/' . APPLICATION_SITE . '/vendor/autoload.php';

    if (!class_exists(Application::class)) {
        throw new RuntimeException(
            "Unable to load application.\n"
            . "- Type `composer install` if you are developing locally.\n"
            . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
            . "- Type `docker-compose run laminas composer install` if you are using Docker.\n"
        );
    }
    // System constant
    require CONFIG_PATH . '/application.constant.php';
    require_once LIBRARY_PATH . '/mobiledetect/mobiledetectlib/src/MobileDetect.php';

    // Get DEVICE ENV
    if (empty($deviceEnv)) {
        $detect = new Detection\MobileDetect();
        //new object
        $deviceEnv = $detect->getEnv();

    } elseif (!in_array($deviceEnv, array(1, 2, 4))) {
        $deviceEnv = 4; //default pc
    }

    // Retrieve configuration
    $appConfig = ArrayUtils::merge(
        require CONFIG_PATH . '/application.config.php',
        require CONFIG_PATH . '/sites/' . APPLICATION_SITE . '.php'
    );
    if (file_exists(CONFIG_PATH . '/development.config.php')) {
        $appConfig = ArrayUtils::merge($appConfig, require CONFIG_PATH . '/development.config.php');
    }

    //define device env
    $deviceEnv = intval($deviceEnv) > 0 ? $deviceEnv : 4;
    defined('DEVICE_ENV') || define('DEVICE_ENV', $deviceEnv);

    try {
        Application::init($appConfig)->run();
    } catch (\Throwable $e) {
        dd($e->getMessage(), $e->getTraceAsString(), __FUNCTION__, __FILE__);
        die;
    }
} catch (\Throwable $e) {
    var_dump("<pre>", $e->getMessage(), $e->getTraceAsString(), __FUNCTION__, __FILE__);
    exit();
}