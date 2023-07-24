<?php
$dirName = APPLICATION_PATH . '/../layouts/' . APPLICATION_SITE;
return [
    'auto-save-query-action' => [
        'index' => true,
    ],
    'app_resource' => [
        'publicPath'        => PUBLIC_PATH,
        'skinDirectory'     => APPLICATION_SKIN_NAME,
        'uploadDirectory'   => FOLDER_UPLOAD_BY_SITE,
        'cacheDirectory'    => DATA_PATH . '/cache',
        'siteName'          => APPLICATION_SITE,
        'types'             => ['css' => 'css', 'fonts' => 'fonts', 'images' => 'images', 'js' => 'js', 'lib' => 'lib']
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'        => $dirName . '/layout.phtml',
            'layout/iframe'        => $dirName . '/iframe.phtml',
            'layout/empty'         => $dirName . '/empty.phtml',
            'layout/login'         => $dirName . '/login.phtml',
            'layout/error'         => $dirName . '/error.phtml',
            'error/404'            => $dirName . '/404.phtml',
            'error/index'          => $dirName . '/error-index.phtml',
            'error/access-deny'    => $dirName . '/access-deny.phtml',
            'error/log-deny'       => $dirName . '/log-deny.phtml',
            'error/file_404'       => $dirName . '/file-404.phtml',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
    // The following key allows to define custom styling for FlashMessenger view helper.
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
];
