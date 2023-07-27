<?php
namespace Group;

//use \Zf\Ext\Router\RouterLiteral;
use \Zf\Ext\Router\RouterSegment;

return [
    'router' => [
        'routes' => [
            'group-area' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route'    => '/group-area[/:action[/:pid[/:id]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'pid' 	 => '[0-9]*',
                        'id'     => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ]
                ],
            ],
            'group-skill' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route'    => '/group-skill[/:action[/:pid[/:id]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'pid' 	 => '[0-9]*',
                        'id'     => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\SkillController::class,
                        'action'     => 'index',
                    ]
                ],
            ],
            'direct-proposal' => [
                'type'    => RouterSegment::class,
                'options' => [
                    'route'    => '/direct-proposal[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\DirectProposalController::class,
                        'action'     => 'index',
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
        ],
        'invokables' => [
            Controller\IndexController::class,
            Controller\SkillController::class,
            Controller\DirectProposalController::class,
        ]
    ],
    'access_filter' => [
        'options' => [
            'mode' => 'restrictive'
        ],
        'controllers' => []
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
