<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3c6b7defc5c46616a5aa15107ab16ad6
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WOOFP\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WOOFP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3c6b7defc5c46616a5aa15107ab16ad6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3c6b7defc5c46616a5aa15107ab16ad6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3c6b7defc5c46616a5aa15107ab16ad6::$classMap;

        }, null, ClassLoader::class);
    }
}
