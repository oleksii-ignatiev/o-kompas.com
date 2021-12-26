<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* https://codex.wordpress.org/Creating_Tables_with_Plugins */


function wptournreg_install() {
	global $wpdb;

	add_option( 'wptournreg_db_version', WP_TOURNREG_DB_VER );
	$table_name = WP_TOURNREG_DATA_TABLE;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		tournament_id varchar(32) NOT NULL,
		time int(64) DEFAULT 0 NOT NULL,
		lastname tinytext DEFAULT '' NOT NULL,
		firstname tinytext DEFAULT '' NOT NULL,
		email tinytext NOT NULL,
		phone1 varchar(64) DEFAULT '' NOT NULL,
		phone2 varchar(32) DEFAULT '' NOT NULL,
		rating1 mediumint(9) DEFAULT NULL,
		rating2 mediumint(9) DEFAULT NULL,
		affiliation tinytext DEFAULT '' NOT NULL,
		message text DEFAULT '' NOT NULL,
		approved bool DEFAULT FALSE NOT NULL,
		protected bool DEFAULT FALSE NOT NULL,
		fee_is_paid bool DEFAULT FALSE NOT NULL,
		gender bool DEFAULT FALSE NOT NULL,
		birthyear mediumint(9) DEFAULT NULL,
		postcode varchar(12) NOT NULL,
		city varchar(32) NOT NULL,
		address varchar(128) NOT NULL,
		ip varchar(32) NOT NULL,
		custom1 tinytext DEFAULT '' NOT NULL,
		custom2 tinytext DEFAULT '' NOT NULL,
		custom3 tinytext DEFAULT '' NOT NULL,
		custom4 tinytext DEFAULT '' NOT NULL,
		custom5 tinytext DEFAULT '' NOT NULL,
		hash varchar(32) DEFAULT NULL,
		UNIQUE (hash),
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}