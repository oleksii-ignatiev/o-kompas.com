<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* updates from editor */
function wptournreg_update_data( ) {
	
	global $wpdb;

	require_once WP_TOURNREG_DATABASE_PATH . 'escape.php';
	require_once WP_TOURNREG_DATABASE_PATH . 'scheme.php';
	$scheme = wptournreg_get_field_list();
		
	$values = [];
	$placeholder = [];
	foreach( $scheme as $field => $type ) {
		
		if ( $field == 'id' || $field == 'time' || $field == 'ip' || !isset( $_POST[ $field ] ) ) {
		}
		else if ( $field == 'email' ) {
			
			$values[ $field ][ 'value' ] =  wptournreg_escape( sanitize_email( $_POST[ $field ] ) );
			$values[ $field ][ 'placeholder'] = '%s';
		}
		else if ( $field == 'message' ) {
			
			$values[ $field ][ 'value' ] =  wptournreg_escape( sanitize_textarea_field( $_POST[ $field ] ) );
			$values[ $field ][ 'placeholder'] = '%s';
		}
		else if ( preg_match( '/bool|int\(1\)/i', $type ) ) {
			
			$values[ $field ][ 'value' ] = ( isset( $_POST[ $field ] ) ) ? 1 : 0;
			$values[ $field ][ 'placeholder'] = '%d';
		}
		else if ( preg_match( '/int\(/i', $type ) ) {
			
			if ( !empty( $_POST[ $field ] ) ) {
			
				$values[ $field ][ 'value' ] = ( int ) sanitize_text_field( $_POST[ $field ] );
				$values[ $field ][ 'placeholder'] = '%d';
			}
			else {
				$values[ $field ][ 'placeholder'] = 'NULL';
			}
		}
		else {
			
			$values[ $field ][ 'value' ] =  wptournreg_escape( sanitize_text_field( $_POST[ $field ] ) );
			$values[ $field ][ 'placeholder'] = '%s';		
		}
	}
	
	$update = [];
	$paceholders = [];
	foreach( $values as $field => $value ) {
		
		if ( $value[ 'placeholder' ] != 'NULL' ) {
			
			$update[] = $value[ 'value' ];
		}
		$placeholders[] = $field . ' = ' . $value[ 'placeholder' ];
	}
	$update[] = sanitize_text_field( $_POST[ 'id' ] );
	
	/* $wpdb->update and friends can't set NULL values */
	return $wpdb->query( $wpdb->prepare( 'UPDATE ' . WP_TOURNREG_DATA_TABLE . ' SET ' . implode( ', ', $placeholders ) . ' WHERE id = %d;' , $update ) );
}