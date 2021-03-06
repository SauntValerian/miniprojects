<?php
/**
 * Plugin Name: Auto-load Next Post
 * Plugin URI: http://premium.wpmu.org
 * Description: Automatically loads the next (actually previous) post when the navigation link scrolls into view. Makes use of Scrollspy jQuery plugin
 * Version: The Plugin's Version Number, e.g.: 1.0
 * Author: Chris Knowles
 * Author URI: http://premium.wpmudev.org
 * License: GPL2
 */

/**
* Add Javascript files
**/

function alnp_enqueue_scripts() {

	wp_enqueue_script( 'scrollspy', plugins_url() . '/autoloadpost/js/scrollspy.js', array('jquery'), null, true );
	wp_enqueue_script( 'autoloadpost', get_stylesheet_directory_uri() . '/js/autoloadpost.js', array('scrollspy'), null, true );
	wp_enqueue_script( 'history' , plugins_url() . '/autoloadpost/js/jquery.history.js', array('jquery'), null, true );

}

if ( is_singular ) {
	add_action( 'wp_enqueue_scripts', 'alnp_enqueue_scripts', 10 );
}


/** 
*  Add the endpoint for the call to get the post html only
**/

function alnp_add_endpoint() {
    add_rewrite_endpoint( 'partial', EP_PERMALINK );
}

add_action( 'init', 'alnp_add_endpoint' );

/**
* When /partial endpoint is used on a post, get just the post html
**/
function alnp_template_redirect() {
    global $wp_query;
 
    // if this is not a request for partial or a singular object then bail
    if ( ! isset( $wp_query->query_vars['partial'] ) || ! is_singular() )
        return;
 
	// include custom template
    include get_stylesheet_directory() . '/content-partial.php';

    exit;
}

add_action( 'template_redirect', 'alnp_template_redirect' );

function partial_endpoints_activate() {

    // ensure our endpoint is added before flushing rewrite rules
    alnp_add_endpoint();
    
    // flush rewrite rules - only do this on activation as anything more frequent is bad!
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'partial_endpoints_activate' );
 

function partial_endpoints_deactivate() {
    // flush rules on deactivate as well so they're not left hanging around uselessly
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'partial_endpoints_deactivate' );