<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function wptournreg_load_tablesort() {
	
	wp_register_script(
		'wptournregtablesorter', // $handle
		WP_TOURNREG_TBSORTJS_URL, // $url
		array( 'jquery' ), // $deps
		WP_TOURNREG_TBSORT_VER, // $ver
		true // $in_footer
	);
		
	wp_register_style(
		'wptournregtablesorter', // $handle
		WP_TOURNREG_TBSORTCSS_URL, // $url
		array(), // $deps
		WP_TOURNREG_TBSORT_VER, // $ver
		false // $in_footer
	);
}

/* add  scripts and styles */  
add_action( 'wp_enqueue_scripts', 'wptournreg_load_tablesort', 9999 );
