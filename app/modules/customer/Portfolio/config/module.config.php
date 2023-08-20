<?php

declare(strict_types=1);

namespace Portfolio;

use \Zf\Ext\Router\RouterLiteral;
use \Zf\Ext\Router\RouterSegment;

return [
    'router' => [
        'routes' => [
            'portfolio' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route'    => '/portfolio[/:id]',
                    'constraints' => [
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'works' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route' => '/works/:param[-:id][.html]',
                    'constraints' => [
                        'param' => '[a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'detail'
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
