<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* sanitizes and escapes the form data
 * param 1: the raw field value
 * return: sanitized and escaped field value */
function wptournreg_escape( $value ) {
	
	global $wpdb;
	
	return $wpdb->_real_escape( $value );
}