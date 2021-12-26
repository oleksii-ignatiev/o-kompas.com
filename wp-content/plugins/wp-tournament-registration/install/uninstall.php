<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Delete table on uninstall. */

function wptournreg_uninstall() {
	
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS " . WP_TOURNREG_DATA_TABLE );
	delete_option( 'wptournreg_db_version' );
}