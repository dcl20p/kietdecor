<?php
$dateFormat = require CONFIG_PATH . '/date_configs.php';
return [
    // -- Manager site
	[
		[
		    'www.manager.kietdecor.local', 'manager.kietdecor.local',
		],
		[
		    '/en' => $base = [ 
		        'site'          => 'manager',
		        'upload-folder' => 'manager',
		        'ss_path'       => 'manager',
		        'csrf_token_dir'=> 'manager',
		        'language'      => 'en',
		        'locale'        => 'en_US',
		        'skin-name'     => 'assets/manager',
		        'date'          => $dateFormat['en']
		    ],
		    '/ja' => array_replace($base, [
		        'language'      => 'ja',
		        'locale'        => 'ja_JP',
		        'date'          => $dateFormat['ja']
		    ]),
		    '/' => array_replace($base, [
		        'language'      => 'vi',
		        'locale'        => 'vi_VN',
		        'date'          => $dateFormat['vi']
		    ]),
		],
	],
    // --- Customer site
    [
        [
            'www.kietdecor.local', 'kietdecor.local',
            'www.img.kietdecor.local', 'img.kietdecor.local'
        ],
        [
            '/en' => $base = [ 
                'site'          => 'customer',
                'upload-folder' => 'customer',
                'ss_path'       => 'customer',
                'csrf_token_dir'=> 'customer',
                'language'      => 'en',
                'locale'        => 'en_US',
                'skin-name'     => 'assets/customer',
                'date'          => $dateFormat['en']
            ],
            '/ja' => array_replace($base, [
		        'language'      => 'ja',
		        'locale'        => 'ja_JP',
		        'date'          => $dateFormat['ja']
		    ]),
            '/' => array_replace($base, [
                'language'      => 'vi',
                'locale'        => 'vi_VN',
                'date'          => $dateFormat['vi']
            ]),
        ],
    ],
];
?>