<?php

declare(strict_types=1);

namespace Project;

use \Zf\Ext\Router\RouterSegment;
return [
    'router' => [
        'routes' => [
            'project' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route'    => '/project[/:action[/:id]]',
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
