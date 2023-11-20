<?php
namespace Portfolio\Controller\Factory;
use Portfolio\Controller\IndexController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, ?array $option = null)
    {
        $phpRenderer = $container->get('Laminas\View\Renderer\PhpRenderer');

        // Instance the controller
        return new IndexController($phpRenderer);
    }
}