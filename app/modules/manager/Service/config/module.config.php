<?php

declare(strict_types=1);

namespace Service;

use \Zf\Ext\Router\RouterSegment;
return [
    'router' => [
        'routes' => [
            'service' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route'    => '/service[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
        ],
        'invokables' => [
            Controller\IndexController::class
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
