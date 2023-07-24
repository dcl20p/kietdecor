<?php
namespace Application\Service\Factory;

use Psr\Container\ContainerInterface;
use Application\Service\NavManager; 
use Laminas\Authentication\AuthenticationService;

/**
 * This is the factory class for NavManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class NavManagerFactory
{
    /**
     * This method creates the NavManager service and returns its instance. 
     * 
     * @return NavManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NavManager
    {
        // Set $authen to null initially
        $authen = null;
        
        // Only set $authen to identity if AuthenticationService is found in container
        if ($container->has(AuthenticationService::class)) {
            $authen = $container->get(AuthenticationService::class)->getIdentity();
        }
        
        // Create NavManager instance and pass in dependencies by constructor injection
        return new NavManager(
            $container->get('ViewHelperManager')->get('zfUrl'),
            $authen
        );
    }
}