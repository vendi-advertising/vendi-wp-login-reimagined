<?php

namespace Vendi\WPLoginReimagined\Screens;

use Vendi\Shared\utils;

abstract class base_screen_with_post extends base_screen
{
    private $_local_post;

    private $_fields;

    abstract public function handle_post();

    public function pre_process()
    {

    }

    public function __construct( array $local_get = null, array $local_post = null )
    {
        parent::__construct( $local_get );

        if( ! $local_post )
        {
            $local_post = [];
        }

        $this->_local_post = $local_post;
    }

    public function process()
    {

        $this->pre_process();

        if( utils::is_post() )
        {
            //TODO: utils::get_request_object() returns NULL for missing
            //TODO: Wrap with IF?
            $this->handle_post( );
        }

        $this->handle_get();
    }
}
