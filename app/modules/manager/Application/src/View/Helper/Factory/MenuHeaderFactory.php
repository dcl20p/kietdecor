<?php
namespace Application\View\Helper\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\View\Helper\MenuHeader;

class MenuHeaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = $container->get('ControllerPluginManager');
        $entityManager = $controller->get('getEntityManager');
        unset($controller);

        return new MenuHeader($entityManager);
    }
}
