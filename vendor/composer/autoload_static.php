<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc573b2796d070f562f08be6242955b0b
{
    public static $prefixesPsr0 = array (
        'N' => 
        array (
            'Netcarver\\Textile' => 
            array (
                0 => __DIR__ . '/..' . '/netcarver/textile/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitc573b2796d070f562f08be6242955b0b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}