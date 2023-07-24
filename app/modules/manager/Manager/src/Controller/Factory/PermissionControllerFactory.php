<?php
namespace Manager\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Manager\Controller\PermissionController;

class PermissionControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configs = $container->get('config');
        $routersArr = $routerTypes = [];
        if (!empty($routers = $configs['router']['routes'])) {
            foreach ($routers as $router => $data) {
                if (!empty($controller = $data['options']['defaults']['controller'])
                    && $controller != 'DoctrineORMModule\Yuml\YumlController'
                ) {
                    $routersArr[$controller][] = $router;
                    $routerTypes[$router] = [
                        'type' => $data['type'],
                        'action' => $data['options']['defaults']['action'],
                    ];
                }
            }
        }
        return new PermissionController($routersArr, $routerTypes);
    }
}