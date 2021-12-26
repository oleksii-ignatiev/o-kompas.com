<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* returns an associative array COLUMN_NAME => COLUMN_TYPE of the table scheme (no params) */
function wptournreg_get_field_list() {
	
	global $wpdb;
	
	$sql = 'SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="' . WP_TOURNREG_DATA_TABLE . '";';
	$table_scheme = $wpdb->get_results( $sql );
	
	$fields = [];
	
	
	
	foreach( $table_scheme AS $field ) {
		
		$fields[ $field->{'COLUMN_NAME'} ] = $field->{'COLUMN_TYPE'};
   
	}
	
	return $fields;
	
}