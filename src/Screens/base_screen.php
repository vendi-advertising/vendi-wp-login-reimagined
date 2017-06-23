<?php

namespace Vendi\WPLoginReimagined\Screens

abstract class base_screen
{
    abstract public function get_body_contents();

    final public function render_body()
    {
        ?>
        <body class="login">

        </body>
        <?php
    }

    final public function render_header( )
    {
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
        ?>
        </head>
        <?php
    }

    final public function render_footer()
    {
        ?>
        </html>
        <?php
    }
}
