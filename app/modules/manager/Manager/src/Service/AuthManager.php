<?php
namespace Manager\Service;
use Laminas\Authentication\Result;

/**
 * The AuthManager service is responsible for user's login/logout and simple access 
 * filtering. The access filtering feature checks whether the current visitor 
 * is allowed to see the given page or not.  
 */
class AuthManager
{
    /**
     * Authentication service.
     * @var \Laminas\Authentication\AuthenticationService
     */
    private $authService;
    
    /**
     * Session manager.
     * @var Laminas\Session\SessionManager
     */
    private $sessionManager;
    
    /**
     * Contents of the 'access_filter' config key.
     * @var array 
     */
    private $config;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $sessionManager, ?array $config = null) 
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     *
     * @param string $email
     * @param string $password
     * @param integer $rememberMe
     * @param string $ipAddress
     * @param array $device
     * @return mixed
     */
    public function login(
        string $email, 
        string $password, 
        int $rememberMe, 
        string $ipAddress, 
        array $device
    ): mixed
    {
        if ($this->authService->getIdentity() != null) {
            throw new \Exception('Already logged in');
        }
        
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $authAdapter->setIpAddress($ipAddress);
        $authAdapter->setDevice($device);

        $result = $this->authService->authenticate();

        if ($result->getCode() === Result::SUCCESS) {
            if ($rememberMe) {
               // Session cookie will expire in 1 month (30 days).
               $this->sessionManager->rememberMe(2592000); // 2592000= 60*60*24*30
            }

            $storage = $this->authService->getStorage();
            $obj = $authAdapter->getResultRowObject([
                'adm_id', 'adm_fullname', 'adm_email', 'adm_code',
                'adm_phone', 'adm_avatar', 'adm_username',
                'adm_status', 'adm_groupcode', 'adm_bg_timeline',
                'adm_last_access_time','adm_gender'
            ]);
            $storage->write($obj);

            // Save login log
            $authAdapter->saveLogActive();
            unset($storage, $obj);
        }
        unset($authAdapter);
         
        return $result;
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        if ($this->authService->hasIdentity()) {
            $admId = $this->authService->getIdentity()->adm_id;

            $this->authService->getAdapter()->clearPushToken(
                $admId
            );
            $this->authService->clearIdentity();

            $authAdapter = $this->authService->getAdapter();
            $authAdapter->updateStateLogin(
                $admId, 0
            );
        }
    }

    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     * 
     * This method uses the 'access_filter' key in the config file and determines
     * whenther the current visitor is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     *
     * @param string $controllerName
     * @param string $actionName
     * @return boolean
     */
    public function filterAccess(string $controllerName, string $actionName): bool
    {
        $mode = $this->config['options']['mode'] ?? 'restrictive';

        if (!in_array($mode, ['restrictive', 'permissive'], true)) {
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode)');
        }

        $items = $this->config['controllers'][$controllerName] ?? [];

        foreach ($items as $item) {
            $actionList = $item['actions'];
            $allow = $item['allow'];

            if (is_array($actionList) && in_array($actionName, $actionList, true) || $actionList === '*') {
                if ($allow === '*') {
                    return true;
                } elseif ($allow === '@' && $this->authService?->hasIdentity()) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        if ($mode === 'restrictive' && !$this->authService?->hasIdentity()) {
            return false;
        }

        return true;
    }

}