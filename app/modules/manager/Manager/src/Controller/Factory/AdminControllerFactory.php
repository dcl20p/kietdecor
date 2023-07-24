<?php
namespace Manager\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Manager\Controller\AdminController;
use Manager\Service\AdminManager;

class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $adminManager = $container->get(AdminManager::class);
        
        return new AdminController($adminManager);
    }
}