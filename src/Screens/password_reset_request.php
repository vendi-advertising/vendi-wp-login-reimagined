<?php

namespace Vendi\WPLoginReimagined\Screens;

class password_reset_request extends base_screen_with_post
{
    public function handle_post( )
    {

    }

    public function handle_get()
    {

    }

    public function get_body_contents( bool $echo = true )
    {
        echo '<h1>Password Reset Request</h1>';
    }
}
