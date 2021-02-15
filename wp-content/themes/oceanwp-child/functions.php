<?php
// Enable shortcodes in widget text 
add_filter ('widget_text', 'shortcode_unautop');
add_filter ('widget_text', 'do_shortcode',11);

function test_shortcode(){
   return '<h2>Hello world !</h2>';
}
add_shortcode('hello_world', 'test_shortcode');
function oceanwp_child_enqueue_parent_style() {
	// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
	$theme   = wp_get_theme( 'OceanWP' );
	$version = $theme->get( 'Version' );
	// Load the stylesheet
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );
	
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );