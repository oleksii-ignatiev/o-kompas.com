<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function wptournreg_load_assets() {
	wp_register_script(
		'wptournreg', // $handle
		WP_TOURNREG_JS_URL, // $url
		array( 'jquery', 'wptournregtablesorter' ), // $deps
		WP_TOURNREG_PLUGIN_VER, // $ver
		true // $in_footer
	);
		
	wp_register_style(
		'wptournreg', // $handle
		WP_TOURNREG_CSS_URL, // $url
		array( 'wptournregtablesorter' ), // $deps
		WP_TOURNREG_PLUGIN_VER, // $ver
		false // $in_footer
	);
}

/* add  scripts and styles */  
add_action( 'wp_enqueue_scripts', 'wptournreg_load_assets', 9999 );