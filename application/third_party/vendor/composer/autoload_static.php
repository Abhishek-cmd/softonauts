<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8f2c6d796419c8ff802cb60295ea6c34
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8f2c6d796419c8ff802cb60295ea6c34::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8f2c6d796419c8ff802cb60295ea6c34::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
