<?php

namespace Vendi\WPLoginReimagined\Screens;

class login extends base_screen_with_post
{
    private $_customize_login;

    private $_redirect_to;

    private $_secure_cookie;

    private $_http_post;

    private $_interim_login;

    private $_user;

    public function pre_process()
    {
        $this->_http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
        $this->_interim_login = isset($_REQUEST['interim-login']);
        $this->_secure_cookie = '';
        $this->_customize_login = isset( $_REQUEST['customize-login'] );
        if ( $this->_customize_login ) {
            wp_enqueue_script( 'customize-base' );
        }

        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if ( !empty($_POST['log']) && !force_ssl_admin() ) {
            $user_name = sanitize_user($_POST['log']);
            $user = get_user_by( 'login', $user_name );

            if ( ! $user && strpos( $user_name, '@' ) ) {
                $user = get_user_by( 'email', $user_name );
            }

            if ( $user ) {
                if ( get_user_option('use_ssl', $user->ID) ) {
                    $this->_secure_cookie = true;
                    force_ssl_admin(true);
                }
            }
        }

        if ( isset( $_REQUEST['redirect_to'] ) ) {
            $this->_redirect_to = $_REQUEST['redirect_to'];
            // Redirect to https if user wants ssl
            if ( $this->_secure_cookie && false !== strpos($this->_redirect_to, 'wp-admin') ) {
                $this->_redirect_to = preg_replace('|^http://|', 'https://', $this->_redirect_to);
            }
        } else {
            $this->_redirect_to = admin_url();
        }

    }
    public function handle_post( )
    {
        $reauth = empty($_REQUEST['reauth']) ? false : true;

        $this->_user = wp_signon( array(), $this->_secure_cookie );

        if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
            if ( headers_sent() ) {
                /* translators: 1: Browser cookie documentation URL, 2: Support forums URL */
                $this->_user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.' ),
                    __( 'https://codex.wordpress.org/Cookies' ), __( 'https://wordpress.org/support/' ) ) );
            } elseif ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
                // If cookies are disabled we can't log in even with a valid user+pass
                /* translators: 1: Browser cookie documentation URL */
                $this->_user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.' ),
                    __( 'https://codex.wordpress.org/Cookies' ) ) );
            }
        }

        $requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
        /**
         * Filters the login redirect URL.
         *
         * @since 3.0.0
         *
         * @param string           $redirect_to           The redirect destination URL.
         * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
         * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
         */
        $this->_redirect_to = apply_filters( 'login_redirect', $this->_redirect_to, $requested_redirect_to, $this->_user );

        if ( !is_wp_error($this->_user) && !$reauth ) {
            if ( $this->_interim_login ) {
                $message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
                $this->_interim_login = 'success';
                login_header( '', $message ); ?>
                </div>
                <?php
                /** This action is documented in wp-login.php */
                do_action( 'login_footer' ); ?>
                <?php if ( $this->_customize_login ) : ?>
                    <script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
                <?php endif; ?>
                </body></html>
                <?php
                exit;
            }

            if ( ( empty( $this->_redirect_to ) || $this->_redirect_to == 'wp-admin/' || $this->_redirect_to == admin_url() ) ) {
                // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
                if ( is_multisite() && !get_active_blog_for_user($this->_user->ID) && !is_super_admin( $this->_user->ID ) )
                    $this->_redirect_to = user_admin_url();
                elseif ( is_multisite() && !$this->_user->has_cap('read') )
                    $this->_redirect_to = get_dashboard_url( $this->_user->ID );
                elseif ( !$this->_user->has_cap('edit_posts') )
                    $this->_redirect_to = $this->_user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();

                wp_redirect( $this->_redirect_to );
                exit();
            }
            wp_safe_redirect($this->_redirect_to);
            exit();
        }
    }

    public function handle_get()
    {
        $errors = $this->_user;
        // Clear errors if loggedout is set.
        if ( !empty($_GET['loggedout']) || $reauth )
            $errors = new WP_Error();

        if ( $this->_interim_login ) {
            if ( ! $errors->get_error_code() )
                $errors->add( 'expired', __( 'Your session has expired. Please log in to continue where you left off.' ), 'message' );
        } else {
            // Some parts of this script use the main login form to display a message
            if      ( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
                $errors->add('loggedout', __('You are now logged out.'), 'message');
            elseif  ( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
                $errors->add('registerdisabled', __('User registration is currently not allowed.'));
            elseif  ( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
                $errors->add('confirm', __('Check your email for the confirmation link.'), 'message');
            elseif  ( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
                $errors->add('newpass', __('Check your email for your new password.'), 'message');
            elseif  ( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
                $errors->add('registered', __('Registration complete. Please check your email.'), 'message');
            elseif ( strpos( $this->_redirect_to, 'about.php?updated' ) )
                $errors->add('updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to see what&#8217;s new.' ), 'message' );
        }

        /**
         * Filters the login page errors.
         *
         * @since 3.6.0
         *
         * @param object $errors      WP Error object.
         * @param string $redirect_to Redirect destination URL.
         */
        $errors = apply_filters( 'wp_login_errors', $errors, $this->_redirect_to );

        // Clear any stale cookies.
        if ( $reauth )
            wp_clear_auth_cookie();

        login_header(__('Log In'), '', $errors);

        if ( isset($_POST['log']) )
            $user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(wp_unslash($_POST['log'])) : '';
        $rememberme = ! empty( $_POST['rememberme'] );

        if ( ! empty( $errors->errors ) ) {
            $aria_describedby_error = ' aria-describedby="login_error"';
        } else {
            $aria_describedby_error = '';
        }
    ?>

    <form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
        <p>
            <label for="user_login"><?php _e( 'Username or Email Address' ); ?><br />
            <input type="text" name="log" id="user_login"<?php echo $aria_describedby_error; ?> class="input" value="<?php echo esc_attr( $user_login ); ?>" size="20" /></label>
        </p>
        <p>
            <label for="user_pass"><?php _e( 'Password' ); ?><br />
            <input type="password" name="pwd" id="user_pass"<?php echo $aria_describedby_error; ?> class="input" value="" size="20" /></label>
        </p>
        <?php
        /**
         * Fires following the 'Password' field in the login form.
         *
         * @since 2.1.0
         */
        do_action( 'login_form' );
        ?>
        <p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php checked( $rememberme ); ?> /> <?php esc_html_e( 'Remember Me' ); ?></label></p>
        <p class="submit">
            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Log In'); ?>" />
    <?php   if ( $this->_interim_login ) { ?>
            <input type="hidden" name="interim-login" value="1" />
    <?php   } else { ?>
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($this->_redirect_to); ?>" />
    <?php   } ?>
    <?php   if ( $this->_customize_login ) : ?>
            <input type="hidden" name="customize-login" value="1" />
    <?php   endif; ?>
            <input type="hidden" name="testcookie" value="1" />
        </p>
    </form>

    <?php if ( ! $this->_interim_login ) { ?>
    <p id="nav">
    <?php if ( ! isset( $_GET['checkemail'] ) || ! in_array( $_GET['checkemail'], array( 'confirm', 'newpass' ) ) ) :
        if ( get_option( 'users_can_register' ) ) :
            $registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register' ) );

            /** This filter is documented in wp-includes/general-template.php */
            echo apply_filters( 'register', $registration_url ) . ' | ';
        endif;
        ?>
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?' ); ?></a>
    <?php endif; ?>
    </p>
    <?php }

    $this->maybe_get_js__wp_attempt_focus( $user_login )

    login_footer();
    break;
    }

    public function maybe_get_js__wp_attempt_focus( $user_login )
    {
        /**
         * Filters whether to print the call to `wp_attempt_focus()` on the login screen.
         *
         * @since 4.8.0
         *
         * @param bool $print Whether to print the function call. Default true.
         */
        if ( apply_filters( 'enable_login_autofocus', true ) && ! $error )
        {

            ?>
<script type="text/javascript">
    function wp_attempt_focus(){
    setTimeout( function(){ try{
    <?php
    if ( $user_login ) {
    ?>
    d = document.getElementById('user_pass');
    d.value = '';
    <?php
    } else {
    ?>
    d = document.getElementById('user_login');
    <?php
        if ( 'invalid_username' == $errors->get_error_code() ) {
    ?>
    if( d.value != '' )
    {
        d.value = '';
    }
    <?php
        }
    }
    ?>
    d.focus();
    d.select();
    } catch(e){}
    }, 200);
    }
    <?php
    /**
     * Filters whether to print the call to `wp_attempt_focus()` on the login screen.
     *
     * @since 4.8.0
     *
     * @param bool $print Whether to print the function call. Default true.
     */
    if ( apply_filters( 'enable_login_autofocus', true ) && ! $error ) {
    ?>
    wp_attempt_focus();
    <?php } ?>
    if(typeof wpOnload=='function')wpOnload();
    <?php if ( $this->_interim_login ) { ?>
    (function(){
    try {
        var i, links = document.getElementsByTagName('a');
        for ( i in links ) {
            if ( links[i].href )
                links[i].target = '_blank';
        }
    } catch(e){}
    }());
    <?php } ?>
    </script>
            <?php

        }
    }

    public function get_body_contents( bool $echo = true )
    {
        echo '<h1>Login</h1>';
    }
}
