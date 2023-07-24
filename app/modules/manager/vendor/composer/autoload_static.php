<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitffc9f4513ac91121b737e15739654a53
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Product\\' => 8,
        ),
        'M' => 
        array (
            'Models\\' => 7,
            'Manager\\' => 8,
        ),
        'I' => 
        array (
            'ImageTraits\\' => 12,
        ),
        'A' => 
        array (
            'Application\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Product\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Product/src',
        ),
        'Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../../models',
        ),
        'Manager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Manager/src',
        ),
        'ImageTraits\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../shareModules/ImageTraits',
        ),
        'Application\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Application/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitffc9f4513ac91121b737e15739654a53::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitffc9f4513ac91121b737e15739654a53::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitffc9f4513ac91121b737e15739654a53::$classMap;

        }, null, ClassLoader::class);
    }
}
