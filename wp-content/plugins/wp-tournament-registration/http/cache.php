<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* no caching of rapidly changing documents */
function wptournreg_supress_caching( $headers ) {
	
	global $post;
	
	if ( is_single() && ( has_shortcode( $post->post_content, 'wptournregedit') || has_shortcode( $post->post_content, 'wptournreglist') ) ) {

		header('Cache-Control: private, max-age=0, no-store');
        header('Expires: Thu, 01 Dec 1990 16:00:00 GMT');
	}
}

/* 
	The wp_headers hook fires to early for content checking: https://wordpress.stackexchange.com/questions/258896/how-can-i-change-http-headers-only-to-posts-of-a-specific-category-from-a-plugin 
*/
add_filter( 'template_redirect', 'wptournreg_supress_caching' );