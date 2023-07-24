<?php
namespace Models\Utilities;

use Laminas\Authentication\AuthenticationService;
use Laminas\Config\Writer\PhpArray;
use Laminas\Mime\Mime;
use Laminas\Mvc\MvcEvent;
use Models\Entities\Constant;
use stdClass;
use Zf\Ext\Utilities\ZFTransportSmtp;

class AppUtilities
{
    /**
     * Send mail by template code
     * @param string $tmplCode
     * @param \Doctrine\ORM\EntityManager $dataAdapter
     * @param array $keys
     * @param array $vals
     * @param array $to
     * @param array $attachment
     * @return bool
     * @throws Exception
     */
    public static function sendMailByTmplCode(
        string $tmplCode, $dataAdapter, array $key = [],
        array $vals = [], array $to = [], array $attachment = []
    ): bool
    {
        $tmpl = $dataAdapter->getRepository(Constant::class)
            ->fetchTmplMail($tmplCode);
        
        if (empty($tmpl)) return false;

        $tmpl['content'] = str_replace(
            $key, $vals, $tmpl['content'] ?? ''
        );

        if (empty($to)) {
            $to = [
                'email' => $tmpl['receiver'] ?? '',
                'name'  => DOMAIN_NAME
            ];
        }

        return ZFTransportSmtp::sendMail([
            'to'        => $to['email'],
            'toName'    => $to['name'],

            'from'      => SIGN_UP_EMAIL,
            'fromName'  => $tmpl['sender'],

            'replyTo'   => NO_REPLY_EMAIL,
            'title'     => $tmpl['title'] ?? '',
            'msg'       => $tmpl['content'],
            'attachment'=> $attachment,
            'encoding'  => Mime::ENCODING_QUOTEDPRINTABLE
        ], $dataAdapter->getConnection());
    }

    /**
     * Save error log system
     *
     * @param MvcEvent $event
     * @return boolean|void
     */
    public static function logErrors(MvcEvent $event)
    {
        if ($event->getParam('exception')) {
            $error = $event->getParam('exception');
            try {
                $serviceManager = $event->getApplication()->getServiceManager();
                $user = self::getAuthenFromZendEvt($event);
                $request = $event->getApplication()->getRequest();

                $params = [
                    'post' => $request->getPost()->toArray(),
                    'get' => $request->getQuery()->toArray(),
                ];
                $pathApp = realpath(APPLICATION_PATH . '/../../');
                $pathLib = realpath(LIBRARY_PATH . '/../');
                $pathPub = realpath(PUBLIC_PATH);

                $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
                $entityManager->getConnection()
                    ->insert('tbl_error', [
                        'error_user_id' => ($user ? ($user->{$user->authen_key ?? 'user_id'} ?? null) : null),
                        'error_uri'     => $uri = ((string)$request->getUri()->getPath()),
                        'error_params'  => @json_encode($params),
                        'error_method'  => ($request->isPost() ? 'POST' : 'GET'),
                        'error_msg'     => $eMessage = 'Message: '. str_replace([$pathApp, $pathPub, $pathLib], '', $error->getMessage())
                        . ".\nOn line: "  . $error->getLine()
                        . ".\nOf file: " . str_replace([$pathApp, $pathPub, $pathLib], '', $error->getFile()),
                        'error_trace'   => str_replace([$pathApp, $pathPub, $pathLib], '', $error->getTraceAsString()),
                        'error_code'    => $error->getCode(),
                        'error_time'    => time()
                    ]);

                if (defined('ERROR_AUTO_SEND_MAIL')
                    && !empty(ERROR_AUTO_SEND_MAIL)
                ) {
                    try {
                        return ZFTransportSmtp::sendMail([
                            'to'        => EMAIL_RECEIVE_ERROR,
                            'toName'    => 'System Administator',
                            
                            'from'      => SIGN_UP_EMAIL,
                            'fromName'  => DOMAIN_NAME ?? 'System',
                            
                            'replyTo'   => NO_REPLY_EMAIL,
                            'title'     => 'Your service got an error. Please check it',
                            'msg'       => $eMessage,
                            'encoding'  => Mime::ENCODING_QUOTEDPRINTABLE
                        ], $entityManager->getConnection());
                    } catch (\Throwable $e) {}
                } else return false;
                unset($serviceManager, $request, $entityManager);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Get authentication
     *
     * @param MvcEvent $event
     * @return mixed
     */
    public static function getAuthenFromZendEvt(MvcEvent $event): mixed
    {
        $user = new \stdClass();
        try {
            $serviceManager = $event->getApplication()->getServiceManager();
            if ($serviceManager->has(AuthenticationService::class)) {
                $authService = $serviceManager->get(AuthenticationService::class);
                if ($authService->hasIdentity()) {
                    $user = $authService->getIdentity();
                }
                unset($authService);
            }
            unset($serviceManager);
        } catch (\Throwable $e) {}
        return $user;
    }

    /**
     * Get namespace of class
     *
     * @param string $className
     * @return string
     */
    public static function getNamespacesOfClass(string $className): string
    {
        $arr = explode('\\', $className);
        return array_shift($arr);
    }

    /**
     * Scan comment
     *
     * @param string $comment
     * @param string $type
     * @return mixed
     */
    public static function scanComment(string $comment, string $type = 'module'): mixed
    {
        $rs =[];
        if (empty($comment)) return [];

        switch ($type) {
            case 'module':
                $regex = "/@moduleTitle:(.*)/m";
                break;
            case 'class':
                $regex = "/@controllerTitle:(.*)|\@deprecatedPermission:(.*)/m";
                break;
            case 'action':
                $regex = "/\@actionTitle\:(.*)|\@actionDescription\:(.*)|\@deprecatedPermission:(.*)/m";
                break;
            default:
                return $rs;
        }

        $matches = [];
        if (preg_match_all($regex, $comment, $matches)) {
            foreach ($matches[0] ?? [] as $key => $comment) {
                $tmp = explode(':', $comment, 2);
                if (count($tmp) > 1) {
                    $key = str_replace([
                        '@module', '@controller', '@action'
                    ], '', trim($tmp[0]));
                    $rs[strtolower($key)] = trim($tmp[1]);
                }
            }
        }
        return $rs;
    }

    /**
     * Get all action of controller
     *
     * @param array $routers
     * @param array $routerTypes
     * @param boolean $useCache
     * @return array
     */
    public static function extractAllActionOfCtr(array $routers, array $routerTypes, bool $useCache = true): array
    {
        $rs = [];
        $writer = new PhpArray();
        $writer->setUseBracketArraySyntax(true);

        foreach ($routers as $className => $data) {
            $moduleName = self::getNamespacesOfClass($className);

            // Module
            if (!isset($rs[$moduleName])) {
                $moduleCtms = new \ReflectionClass($moduleName . '\Module');
                $moduleCtms = self::scanComment($moduleCtms->getDocComment(), 'module');
                $moduleCtms['name'] = $moduleName;
                $rs[$moduleName] = $moduleCtms;
            }

            // Controller
            $class = new \ReflectionClass($className);
            $controllerCtms = self::scanComment($class->getDocComment(), 'class');

            $path = [];

            foreach ($data as $router) {
                $path[$router] = implode(DIRECTORY_SEPARATOR, [
                    DATA_PATH, 'reflec_cache', APPLICATION_SITE, crc32($router) . '.php'
                ]);

                if ($useCache && file_exists($path[$router])
                    && !empty($cacheData = include $path[$router])
                ) {
                    $rs[$moduleName]['items'][$router] = $cacheData;
                    unset($cacheData); continue;
                }

                if (!isset($controllerCtms['@deprecatedpermission'])) {
                    $controllerCtms['name'] = $router;
                    $rs[$moduleName]['items'][$router] = $controllerCtms;
    
                    // Action
                    $className = ltrim($className, '\\');
                    $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
    
                    foreach ($methods as $method) {
                        if (
                            $method->getModifiers() == 1 &&
                            $method->class != 'Laminas\Mvc\Controller\AbstractActionController' &&
                            str_ends_with($method->name, 'Action')
                        ) {
                            $actionCtms = self::scanComment($method->getDocComment(), 'action');
                            if (!isset($actionCtms['@deprecatedpermission'])) {
                                if ($routerTypes[$router]['type'] == 'Zf\Ext\Router\RouterLiteral') {
                                    if (stripos($method->name, str_replace('-', '',
                                        $routerTypes[$router]['action'])) !== false) {
                                        $actionCmts['name'] = $method->name;
                                        $rs[$moduleName]['items'][$router]['title'] = $router;
                                        $rs[$moduleName]['items'][$router]['items'][$method->name] = $actionCmts;
                                        break;
                                    }
                                } else {
                                    $actionCmts['name'] = $method->name;
                                    $rs[$moduleName]['items'][$router]['title'] = $router;
                                    $rs[$moduleName]['items'][$router]['items'][$method->name] = $actionCmts;
                                }
                            }
                        }
                    }
                    $writer->toFile($path[$router], $rs[$moduleName]['items'][$router]);
                }
            }
        }
        return ['modules' => $rs];
    }
}