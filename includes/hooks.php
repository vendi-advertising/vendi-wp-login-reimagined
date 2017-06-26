<?php

// add_action(
//                 'login_init',
//                 function()
//                 {
//                     \Vendi\WPLoginReimagined\screen_router::get_instance()
//                         ->get_screen_from_url()
//                         ->process()
//                     ;

//                     // exit;
//                 },
//                 0
// );


add_action(
            'wp_authenticate',
            function( $credentials, $secure_cookie )
            {
                dump( $credentials );
                dump( $secure_cookie );
                die;
            },
            10000,
            2
        );
