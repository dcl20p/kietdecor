<?php
return [
    [
        'id'    => 'dashboard',
        'label' => 'Tổng quan',
        'render'=> 'Subs',
        'icon'  => 'dashboard',
        'subs'  => [
            [
                'id'    => 'home',
                'label' => 'Trang chủ',
                'link'  => ['home'],
                'routeName' => 'home',
                'render'=> 'Link',
                'icon' => 'adjust',
            ],
        ]
    ],
    [
        'label' => 'Tác vụ',
        'render' => 'Title'
    ],
    [
        'id'    => 'projects',
        'label' => 'Dự án',
        'render'=> 'Subs',
        'icon'  => 'inventory_2',
        'subs'  => [
            [
                'id'    => 'project',
                'label' => 'Tất cả dự án',
                'link'  => ['project'],
                'routeName' => 'project',
                'render'=> 'Link',
                'icon' => 'adjust',
            ],
            [
                'id'    => 'project_cate',
                'label' => 'Loại dự án',
                'link'  => ['project', ['action' => 'list-cate']],
                'routeName' => 'project',
                'render'=> 'Link',
                'icon' => 'adjust',
            ],
            [
                'id'    => 'service',
                'label' => 'Dịch vụ',
                'link'  => ['service'],
                'routeName' => 'service',
                'render'=> 'Link',
                'icon' => 'adjust',
            ],
            
        ]
    ],
    [
        'id'    => 'admin-user',
        'label' => 'Người dùng',
        'render'=> 'Subs',
        'icon'  => 'person',
        'subs'  => [
            [
                'id'    => 'user_list',
                'label' => 'Danh sách',
                'link'  => ['admin-user'],
                'routeName' => 'admin-user',
                'render'=> 'Link',
                'icon' => 'adjust',
            ],
        ]
    ],
    [
        'label' => 'Administrator',
        'render' => 'Title'
    ],
    [
        'id'    => 'system',
        'label' => 'Hệ thống',
        'render'=> 'Subs',
        'icon'  => 'settings',
        'subs'  => [
            [
                'id'    => 'system_log',
                'label' => 'Lỗi hệ thống',
                'link'  => ['system_log'],
                'routeName' => 'system_log',
                'render'=> 'Link',
                'icon' => 'error',
            ],
            [
                'id'    => 'system_log_email',
                'label' => 'Lỗi email',
                'link'  => ['system_log_email'],
                'routeName' => 'system_log_email',
                'render'=> 'Link',
                'icon' => 'unsubscribe',
            ],
            [
                'id'    => 'system_permission',
                'label' => 'Phân quyền',
                'link'  => ['permission'],
                'routeName' => 'permission',
                'render'=> 'Link',
                'icon' => 'lock_person',
            ],
        ]
    ],
];