<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf3e122e33a5729d6830263aa9af08853
{
    public static $prefixesPsr0 = array (
        'F' => 
        array (
            'Flow' => 
            array (
                0 => __DIR__ . '/..' . '/flowjs/flow-php-server/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitf3e122e33a5729d6830263aa9af08853::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitf3e122e33a5729d6830263aa9af08853::$classMap;

        }, null, ClassLoader::class);
    }
}
