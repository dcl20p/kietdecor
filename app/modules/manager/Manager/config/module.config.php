<?php

declare(strict_types=1);

namespace Manager;

use \Zf\Ext\Router\RouterSegment;
use \Zf\Ext\Router\RouterLiteral;

return [
    'router' => [
        'routes' => [
            'system_log' => [
                'type' => RouterSegment::class,
                'options' => [
                    'route'    => '/log-error[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\LogErrorController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'system_log_email' => [
                'type' => RouterSegment::class,
                'options' => [
                    'route'    => '/log-error-email[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\LogErrorEmailController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'login' => [
                'type' => RouterLiteral::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'login'
                    ],
                ],
            ],
            'login-deny' => [
                'type' => RouterLiteral::class,
                'options' => [
                    'route'    => '/login-deny',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'login-deny'
                    ],
                ],
            ],
            'logout' => [
                'type' => RouterLiteral::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => RouterLiteral::class,
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'reset-password',
                    ],
                ],
            ],
            'profile' => [
                'type' => RouterLiteral::class,
                'options' => [
                    'route'    => '/profile',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'profile',
                    ],
                ],
            ],
            'permission' => [
                'type' => RouterSegment::class,
                'options' => [
                    'route'    => '/permission[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\PermissionController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'admin-user' => [
                'type' => RouterSegment::class,
                'options' => [
                    'route'    => '/admin-user[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'header' => [
                'type' => RouterSegment::class,
                'options' => [
                    'route'    => '/header[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\FEMenuController::class,
                        'action'     => 'index',
                        'level'      => 0
                    ],
                ],
            ],

        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\AdminController::class => Controller\Factory\AdminControllerFactory::class,
            Controller\PermissionController::class => Controller\Factory\PermissionControllerFactory::class,
        ],
        'invokables' => [
            Controller\IndexController::class => Controller\IndexController::class,
            Controller\LogErrorController::class => Controller\LogErrorController::class,
            Controller\LogErrorEmailController::class => Controller\LogErrorEmailController::class,
            Controller\AdminController::class => Controller\AdminController::class,
            Controller\UserController::class => Controller\UserController::class,
            Controller\FEMenuController::class => Controller\FEMenuController::class,
        ]
    ],
    'access_filter' => [
        'controllers' => [
            Controller\IndexController::class => [
                [
                    'actions' => ['login', 'login-deny', 'reset-password'], 'allow'   => '*'
                ]
            ],
        ],
        'options' => [
            'mode' => 'restrictive'
        ],
    ],
    'service_manager' => [
        'factories' => [
            \Laminas\Authentication\AuthenticationService::class => \Manager\Service\Factory\AuthenticationServiceFactory::class,
            \Manager\Service\AuthAdapter::class => \Manager\Service\Factory\AuthAdapterFactory::class,
            \Manager\Service\AuthManager::class => \Manager\Service\Factory\AuthManagerFactory::class,
            \Manager\Service\AdminManager::class => \Manager\Service\Factory\AdminManagerFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            
        ],
        'aliases' => [
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
