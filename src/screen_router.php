<?php

namespace Vendi\WPLoginReimagined

final class screen_router
{
    private static $_instance = null;

    private $_screens = [];

    public static function get_instance()
    {
        if( ! self::$_instance )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct()
    {
        $this->_screens = [
                            'login'                  => '\Vendi\WPLoginReimagined\Screens\login',
                            'logout'                 => '\Vendi\WPLoginReimagined\Screens\logout',
                            'password_reset_request' => '\Vendi\WPLoginReimagined\Screens\password_reset_request'
                        ];

    }
}

