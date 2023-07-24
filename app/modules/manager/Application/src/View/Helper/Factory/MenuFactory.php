<?php
namespace Application\View\Helper\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\View\Helper\MenuLeft;
use Application\Service\NavManager;
use Laminas\Authentication\AuthenticationService;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = $container->get('ControllerPluginManager');
        $aclPer = null;
        if ($controller->has('getPermission')) {
            $aclPer = $controller->get('getPermission');
        }

        $authen = new \stdClass();
        if ($container->has(AuthenticationService::class)) {
            $authen = $container->get(AuthenticationService::class)->getIdentity();
        }

        // Instantiate the helper.
        return new MenuLeft(
            $container->get(NavManager::class)->getMenuItems(),
            $authen, 
            $aclPer
        );
    }
}
