<?php

namespace Vendi\WPLoginReimagined\Screens

class logout extends base_screen
{
    public function init()
    {
        add_action(
                    'login_head',
                    function( $wp_error )
                    {
                        if( empty( $wp_error ) )
                        {
                            return;
                        }

                        /*
                         * Remove all stored post data on logging out.
                         * This could be added by add_action('login_head'...) like wp_shake_js(),
                         * but maybe better if it's not removable by plugins
                         */
                        if ( 'loggedout' == $wp_error->get_error_code() ) {
                            ?>
                            <script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("wp-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
                            <?php
                        }
                    },
                    10,
                    1
                );
    }
}
