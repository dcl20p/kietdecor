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
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
        ]
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\MenuLeft::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\MenuHeader::class => View\Helper\Factory\MenuHeaderFactory::class,
            View\Helper\Toolbars::class => InvokableFactory::class,
            View\Helper\Breadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'menuLeft' => View\Helper\MenuLeft::class,
            'menuHeader' => View\Helper\MenuHeader::class,
            'toolbars' => View\Helper\Toolbars::class,
            'pageBreadcrumbs' => View\Helper\Breadcrumbs::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            // 'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            // 'error/404'               => __DIR__ . '/../view/error/404.phtml',
            // 'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
