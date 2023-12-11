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
    ],
];