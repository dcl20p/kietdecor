<?php
namespace Application\View\Helper\Factory;
use Application\View\Helper\FooterSession;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class FooterSessionFactory implements FactoryInterface
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

        // Instance the helper
        return new FooterSession(
            $entityManager(null, $container), $routeMatch
        );
    }
}

?>