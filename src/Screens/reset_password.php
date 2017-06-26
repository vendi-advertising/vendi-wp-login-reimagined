<?php

namespace Vendi\WPLoginReimagined\Screens;

class reset_password extends base_screen_with_post
{
    public function handle_post( )
    {

    }

    public function handle_get()
    {

    }

    public function get_body_contents( bool $echo = true )
    {
        echo '<h1>Reset Password</h1>';
    }
}
