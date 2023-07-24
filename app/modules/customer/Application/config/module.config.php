<?php

declare(strict_types=1);

namespace Application;

use \Zf\Ext\Router\RouterLiteral;
use \Zf\Ext\Router\RouterSegment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => RouterLiteral::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'price' => [
                'type'    => RouterLiteral::class,
                'options' => [
                    'route'    => '/price',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'price',
                    ],
                ],
            ],
            'about' => [
                'type'    => RouterLiteral::class,
                'options' => [
                    'route'    => '/about',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'about',
                    ],
                ],
            ],
            'contact' => [
                'type'    => RouterLiteral::class,
                'options' => [
                    'route'    => '/contact',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'contact',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
        'invokables' => [
            Controller\IndexController::class
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\NavCustomer::class => Service\Factory\NavCustomerFactory::class,
        ]
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\HeaderSession::class => View\Helper\Factory\HeaderSessionFactory::class,
            View\Helper\FooterSession::class => View\Helper\Factory\FooterSessionFactory::class,
        ],
        'aliases' => [
            'headerSession' => View\Helper\HeaderSession::class,
            'footerSession' => View\Helper\FooterSession::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
