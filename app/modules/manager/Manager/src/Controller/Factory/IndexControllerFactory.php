<?php
namespace Manager\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Manager\Controller\IndexController;
use Manager\Service\AdminManager;
use Manager\Service\AuthManager;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $authService = $container->get(\Laminas\Authentication\AuthenticationService::class);
        $authManager = $container->get(AuthManager::class);
        $adminManager = $container->get(AdminManager::class);
        
        return new IndexController($authManager, $authService, $adminManager);
    }
}