<?php
namespace Manager\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Manager\Service\AdminManager;
use Psr\Container\ContainerInterface;

class AdminManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $entityManager = $container
        ->get('ControllerPluginManager')
        ->get('getEntityManager');
                        
        return new AdminManager($entityManager(null, $container));
    }
}