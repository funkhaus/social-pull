<?php
/**
 * Plugin Name: Social Pull
 * Plugin URI:  http://funkhaus.us/
 * Description: Give authors the ability to connect their social media accounts
 * Version:     1.0
 * Author:      John Robson, Funkhaus
 * Author URI:  http://funkhaus.us
 */

! defined( 'ABSPATH' ) and exit;

    // Helper function to always reference this directory (social-pull plugin directory)
    if ( ! function_exists( 'sp_pd' ) ) {
        function sp_pd() {
	        return trailingslashit( dirname( __FILE__ ) );
	    }
    }

    // start session if needed
    if(session_id() == '') {
        session_start();
    }

    // register 'social' post type
    include_once sp_pd() . 'register-post-type.php';

    // add social post meta
    include_once sp_pd() . 'post-meta.php';

    // add user meta
    include_once sp_pd() . 'user-meta.php';

    // add plugin settings page
    include_once sp_pd() . 'settings.php';

    // add ajax functions
    include_once sp_pd() . 'ajax-functions.php';

?>