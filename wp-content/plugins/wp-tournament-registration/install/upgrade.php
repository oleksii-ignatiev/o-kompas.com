<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Delete table on uninstall. */

function wptournreg_upgrade() {
	
	$db_ver = get_option( 'wptournreg_db_version' );
	
	if ( $db_ver !== false && version_compare( $db_ver, WP_TOURNREG_DB_VER, '<' ) ) {
	
		global $wpdb;
		
		if ( version_compare( $db_ver, '1' ) === 0 ) {
			
			$wpdb->query('ALTER TABLE ' . WP_TOURNREG_DATA_TABLE . ' ADD COLUMN approved BOOL NOT NULL;' );
			$wpdb->query('UPDATE ' . WP_TOURNREG_DATA_TABLE . ' SET approved = 1 WHERE 1;' );
		}
		
		if ( version_compare( $db_ver, '4', '<' ) ) {
			
			$wpdb->query('ALTER TABLE ' . WP_TOURNREG_DATA_TABLE . ' ADD COLUMN hash varchar(32) DEFAULT NULL;' );
			$wpdb->query('ALTER TABLE ' . WP_TOURNREG_DATA_TABLE . ' ADD UNIQUE (hash);' );
		}
		
		update_option( 'wptournreg_db_version', WP_TOURNREG_DB_VER );
	}
	
}