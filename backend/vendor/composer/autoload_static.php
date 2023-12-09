<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit36ea81b261bd294ce5fdfac5e41e0b8b
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Guilherme\\Backend\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Guilherme\\Backend\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit36ea81b261bd294ce5fdfac5e41e0b8b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit36ea81b261bd294ce5fdfac5e41e0b8b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit36ea81b261bd294ce5fdfac5e41e0b8b::$classMap;

        }, null, ClassLoader::class);
    }
}
