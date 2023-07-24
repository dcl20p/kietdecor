<?php
return [
    'zf_permission' => [
        'manager' => [
            'user_key' => 'adm_groupcode',
            'group' => [
                'SUPPORT', 'SUPPER_ADMIN', 'MANAGER', 'STAFF', 'SALE'
            ],
            'prevent_routes' => [
                'access-deny' => true, 'login' => true,
                'logout' => true, 'login-deny' => true,
                'reset-password' => true,
                'profile' => true,
            ],
            'use_subfolder' => true
        ],
        // 'customer' => [
        //     'user_key' => 'ctm_groupcode',
        //     'group' => [
        //         'SUPPORT', 'BOSS', 'MANAGER', 'STAFF', 'SALE'
        //     ],
        //     'prevent_routes' => [
        //         'access-deny' => true, 'login' => true,
        //         'logout' => true, 'login-deny' => true,
        //         'reset-password' => true, 'message' => true,
        //         'change-password' => true, 'company-info' => function($authen){
        //             return empty($authen) ? false : ($authen->ctm_parent_id == '');
        //         },
        //         'profile' => true,
        //         'finish-edit-email' => true,
        //         'edit-profile' => true
        //     ],
        //     'use_subfolder' => true
        // ]
    ],
];