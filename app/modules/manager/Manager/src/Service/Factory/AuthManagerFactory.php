<?php
namespace Manager\Service\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Manager\Service\AuthManager;
use Psr\Container\ContainerInterface;

/**
 * This is the factory class for UserManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class AuthManagerFactory implements FactoryInterface
{
    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $sessionManager = $container->get(SessionManager::class);
        $config = $container->get('config');
        $config = $config['access_filter'] ?? [];

        return new AuthManager($authService, $sessionManager, $config);
    }
}