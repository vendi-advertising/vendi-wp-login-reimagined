<?php

namespace Vendi\WPLoginReimagined\Screens;

class logout extends base_screen_with_post
{
    public function handle_post( )
    {

    }

    public function handle_get()
    {

    }

    public function get_body_contents( bool $echo = true )
    {
        echo '<h1>Logout</h1>';
    }
}
