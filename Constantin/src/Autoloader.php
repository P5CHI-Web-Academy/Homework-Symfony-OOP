<?php

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(
            function ($class) {
                include $class.'.php';
            }
        );
    }
}
