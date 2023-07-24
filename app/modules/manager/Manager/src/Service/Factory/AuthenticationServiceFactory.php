<?php
namespace Manager\Service\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Laminas\Authentication\Storage\Session;
use Laminas\Session\SessionManager;
use Manager\Service\AuthAdapter;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new AuthenticationService(
            new Session(
                Session::NAMESPACE_DEFAULT,
                Session::MEMBER_DEFAULT,
                $container->get(SessionManager::class)
            ),
            $container->get(AuthAdapter::class)
        );
    }
}