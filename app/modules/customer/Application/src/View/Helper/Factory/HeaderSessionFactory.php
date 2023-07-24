<?php
namespace Application\View\Helper\Factory;
use Application\View\Helper\HeaderSession;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Service\NavCustomer;

class HeaderSessionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, ?array $option = null)
    {
        $controller = $container->get('ControllerPluginManager');
        $entityManager = $controller->get('getEntityManager');
        $route = $container->get('router');
        $request = $container->get('request');

        // Get Route match
        $routeMatch = $route->match($request);
        unset($controller, $request, $route);
        $configs = $container->get('config');
        // Instance the helper
        return new HeaderSession(
            $entityManager(null, $container),
            $container->get(NavCustomer::class)->getMenuItems(),
            $configs['facebook'] ?? [], $routeMatch
        );
    }
}

?>