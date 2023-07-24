<?php

declare(strict_types=1);

namespace Manager;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use Manager\Controller\IndexController;
use Manager\Service\AuthManager;
use Models\Utilities\User;

/**
 * @moduleTitle: Manager
 */
class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    /**
     * This method is called once the MVC bootstrapping is complete. 
     *
     * @param MvcEvent $event
     * @return void
     */
    public function onBootstrap(MvcEvent $event): void
    {
        // Get event manager
        $eventManager = $event->getApplication()->getEventManager();
        $shareManager = $eventManager->getSharedManager();

        $shareManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            100
        );
    }

    /**
     * Undocumented function
     *
     * @param MvcEvent $event
     * @return \Laminas\Stdlib\ResponseInterface|void
     */
    public function onDispatch(MvcEvent $event)
    {
        $event->getApplication()
            ->getServiceManager()
            ->get(SessionManager::class)
            ->start();

        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $routeMatch = $event->getRouteMatch();

        $controllerName = $routeMatch->getParam('controller', null);
        $actionName = $routeMatch->getParam('action', null);

        // Get the instance of AuthManager service.
        $authManager = $event->getApplication()->getServiceManager()
            ->get(AuthManager::class);

        if ($controller != IndexController::class
            && !$authManager->filterAccess($controllerName, $actionName)
        ) {
            $request = $event->getApplication()->getRequest();
            if ($request->isXMLHttpRequest()) {
                $response = $event->getApplication()->getResponse();
                $response->setContent('{"success":false,"code":401}');
                return $response;
            }

            $uri = $request->getUri();

            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);
            $uriStr = $uri->toString();
            $redirectUrl = $request->getBaseUrl() . $uriStr;
            $options = [];

            if ($uriStr != '/') {
                $params = (array) $request->getQuery();
                if ($params)
                    $redirectUrl .= '?' . http_build_query($params);
                $redirectUrl = urldecode($redirectUrl);
                $options = ['query' => ['redirectUrl' => $redirectUrl]];
            }

            return $controller->redirectToRoute('login', [], $options);
        }
        $authen = $controller->getAuthen();
        $group = $authen ? $authen->adm_groupcode : '';

        // Sace log access
        if (!empty($group)) {
            User::saveLogAccess($controller, 'adm_id', 'adm_code');
            $this->updateLastAccessTime($authen, $controller);
        }

        // Check permission
        $routeName = $routeMatch->getMatchedRouteName();
        $isValidRoute = in_array($routeName, [
            'access-deny',
            'login',
            'logout',
            'login-deny',
            'reset-password'
        ]);

        $isValidRefer = true;

        $refers = $event->getApplication()->getRequest()
            ->getHeaders('referer', null);
        if ( false == empty($refers) ){
            $refers = $refers->getFieldValue() ?? '';
            $urlParse = parse_url($refers);
            $domain = ltrim($urlParse['host'], 'www.');
            $isValidRefer = $domain == DOMAIN_NAME;
            unset($urlParse, $domain, $refers);
        }
        if (
            false == $isValidRoute
            && false == $controller->getPermission()->checkPermission(
                $routeName, $routeMatch->getParams()
            )
            || (in_array($actionName, ['delete']) && $isValidRefer == false)
        ) {
            $viewRender = $event->getApplication()->getServiceManager()->get('ViewRenderer');
            $respone = $event->getApplication()->getResponse();
            $accessDeny = new ViewModel();

            $accessDeny->setTemplate('error/access-deny');

            $view = new ViewModel(['content' => $viewRender->render($accessDeny)]);
            $view->setTemplate('layout/layout');
            $view->setVariable('zfPermission', $controller->getPermission());

            $respone->setContent($viewRender->render($view));

            unset($viewRender, $view, $accessDeny);
            return $respone;
        }

        $event->getViewModel()->setVariable(
            'zfPermission', $controller->getPermission()
        );
        if ('index' == $actionName) {
            $this->_saveQueryParams(
                [$routeName, $routeMatch->getParam('action', 'index')],
                $controller->params()->fromQuery(),
                $event->getApplication()
                    ->getServiceManager()
                    ->get(SessionManager::class)
            );
        }
    }

    /**
     * Save query params
     * 
     * @param array $opts
     * @param array $params
     * @param SessionManager $ssMn
     * @return void
     */
    protected function _saveQueryParams(array $opts, array $params, SessionManager $ssMn): void
    {
        $container = new Container('queryStringMn');
        $container::setDefaultManager($ssMn);
        $container->offsetSet('queryString', $params);
    }

    /**
     * Update last access datetime to database
     *
     * @param mixed $authen
     * @param mixed $controller
     * @return void
     */
    protected function updateLastAccessTime(mixed $authen, mixed $controller): void
    {
        $time = time();
        $isUpdate = ($time - ($authen->adm_last_access_time ?? 0)) > 300;

        // Auto update after 5 minutes
        if ($isUpdate) {
            try {
                $controller->getEntityManager()
                    ->getConnection()->executeUpdate(
                        'Update tbl_admin set adm_last_access_time = :last_active where adm_id = :adm_id',
                        [
                            'last_active' => $time,
                            'adm_id' => $authen->adm_id
                        ]
                    );
                $authen->adm_last_access_time = $time;
            } catch (\Throwable $e) {
                $controller->saveErrorLog($e);
            }
        }
    }
}
