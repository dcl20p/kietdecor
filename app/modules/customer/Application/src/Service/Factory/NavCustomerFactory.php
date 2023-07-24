<?php
namespace Application\Service\Factory;

use Psr\Container\ContainerInterface;
use Application\Service\NavCustomer; 

/**
 * This is the factory class for NavCustomer service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class NavCustomerFactory
{
    /**
     * This method creates the NavCustomer service and returns its instance. 
     * 
     * @return NavCustomer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NavCustomer
    {   
        // Create NavCustomer instance and pass in dependencies by constructor injection
        return new NavCustomer(
            $container->get('ViewHelperManager')->get('zfUrl')
        );
    }
}