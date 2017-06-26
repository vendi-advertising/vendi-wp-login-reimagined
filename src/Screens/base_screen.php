<?php

namespace Vendi\WPLoginReimagined\Screens;

use Vendi\Shared\utils;

abstract class base_screen
{
    private $_messages = [];

    private $_local_get;

    abstract public function get_body_contents( bool $echo = true );

    public function __construct( array $local_get = null )
    {
        if( ! $local_get )
        {
            $local_get = [];
        }

        $this->_local_get = $local_get;
    }

    final public function add_message( string $message, $extra_classes = [] )
    {
        if( ! is_array( $extra_classes ) )
        {
            $extra_classes = [ $extra_classes ];
        }

        array_unshift( $extra_classes, 'message' );

        $this->_messages[] = [
                                'classes' => $extra_classes,
                                'message' => $message,
                        ];
    }

    public function process()
    {
        $this->handle_get();
    }

    final public function render_body()
    {
        do_action( 'vwplr/pre/body' );

        do_action( 'vwplr/pre/body/' . static::class );
        ?>
        <body class="login">
        <?php
        $this->get_body_contents( true );
        ?>
        </body>
        <?php
    }

    final public function render_header( )
    {
        do_action( 'vwplr/pre/render' );

        do_action( 'vwplr/pre/render/' . static::class );
        ?><!DOCTYPE html>
        <!--[if IE 8]>
            <html xmlns="http://www.w3.org/1999/xhtml" class="ie8" <?php language_attributes(); ?>>
        <![endif]-->
        <!--[if !(IE 8) ]><!-->
            <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
        <!--<![endif]-->
        <head>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
        <title>Title Here</title>
        <?php
        do_action( 'vwplr/render/header' );

        do_action( 'vwplr/render/header/' . static::class );
        ?>
        </head>
        <?php
    }

    final public function render_footer()
    {
        do_action( 'vwplr/pre/footer' );

        do_action( 'vwplr/pre/footer/' . static::class );
        ?>
        </html>
        <?php
    }

    public function handle_get()
    {
        $this->render_header();
        $this->render_body();
        $this->render_footer();
    }
}
