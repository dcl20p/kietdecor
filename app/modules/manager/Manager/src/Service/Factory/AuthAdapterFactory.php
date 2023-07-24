<?php
namespace Manager\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Manager\Service\AuthAdapter;
use Psr\Container\ContainerInterface;

/**
 * This is the factory class for AuthAdapter service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class AuthAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $entityManager = $container->get('ControllerPluginManager')
        ->get('getEntityManager');

        // Create the AuthAdapter and inject dependency to its constructor.
        return new AuthAdapter($entityManager(null, $container));
    }
}