<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Laminas\Session\Storage\SessionArrayStorage;
use \Zf\Ext\Session\RemoteAddr;
use \Zf\Ext\Session\HttpUserAgent;

$redisConf = require CONFIG_PATH . '/redis.configs.php';
return [
    // Session configuration.
    'session_config' => [
        'cookie_domain'   => COOKIE_DOMAIN,
        'cookie_httponly' => true,
        'cookie_secure'   => true,
        'use_cookies'     => true,
        'cookie_lifetime' => ($lifetime = 604800), // 604800 = 7 days
        'gc_maxlifetime'  => $lifetime,
        'php_save_handler'=> 'redis',
        'save_path'       => "tcp://{$redisConf['server']['host']}:{$redisConf['server']['port']}?". http_build_query([
            'auth' 		=> $redisConf['password'],
            'prefix' 	=> 'PHP_SESS_'.APPLICATION_SITE . ':',
            'weight' 	=> 1,
            'timeout' 	=> 2, // => 2 seconds
            'database' 	=> $redisConf['database']
        ]),
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    // Authen key
    'zf_authen_key' => [
        'customer' => 'ctm_id',
        'manager'  => 'adm_id',
    ]
];
