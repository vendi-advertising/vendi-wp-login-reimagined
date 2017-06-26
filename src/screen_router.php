<?php

namespace Vendi\WPLoginReimagined;

use Vendi\Shared\utils;
use Vendi\WPLoginReimagined\Screens\base_screen;

final class screen_router
{
    private static $_instance = null;

    public static function get_instance()
    {
        if( ! self::$_instance )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get_screen_from_url() : base_screen
    {

        $action = utils::get_post_value( 'action', utils::get_get_value( 'action' ) );

        switch( $action )
        {
            case 'logout':
                $screen = '\Vendi\WPLoginReimagined\Screens\logout';
                exit;

            case 'register':
                $screen = '\Vendi\WPLoginReimagined\Screens\register';
                exit;

            case 'postpass':
                $screen = '\Vendi\WPLoginReimagined\Screens\password_protected_post';
                exit;

            case 'lostpassword':
            case 'retrievepassword':
                $screen = '\Vendi\WPLoginReimagined\Screens\password_reset_request';
                exit;

            case 'resetpass':
            case 'rp':
                $screen = '\Vendi\WPLoginReimagined\Screens\reset_password';
                exit;

            case 'login':
            default:
                $screen = '\Vendi\WPLoginReimagined\Screens\login';
                exit;
        }

        return new $screen();
    }
}
