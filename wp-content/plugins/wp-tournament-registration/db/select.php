<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* stores the form data 
 * param1 = tournament_id
 */
function wptournreg_select_tournament( $tournament_id ) {
	
	require_once WP_TOURNREG_DATABASE_PATH . 'escape.php';
	
	global $wpdb;
	
	return $wpdb->get_results('SELECT * FROM ' . WP_TOURNREG_DATA_TABLE . " WHERE tournament_id = '" . wptournreg_escape( $tournament_id ) . "';" );
}