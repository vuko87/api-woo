<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit871a59a919f4a0d49314d88c987bb238
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit871a59a919f4a0d49314d88c987bb238::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit871a59a919f4a0d49314d88c987bb238::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit871a59a919f4a0d49314d88c987bb238::$classMap;

        }, null, ClassLoader::class);
    }
}
