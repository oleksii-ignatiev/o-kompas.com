<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* edit entries */
function wptournreg_edit( $atts = [] ) {
	
	// normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
	
	$a = shortcode_atts( array(
		'display_fields' => null,
		'tournament_id' => null,
	), $atts );
	
	/* error if tournament id is missing */
	if ( empty ( $a[ 'tournament_id' ] ) ) {
		
		return sprintf( __( '%sERROR: Missing %s in shortcode %s!%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', '<kbd>tournament_id</kbd>', '<kbd>wptournregform</kbd>', '</strong>' );
	}
	
	require_once WP_TOURNREG_DATABASE_PATH . 'select.php';
	$result = wptournreg_select_tournament( $a[ 'tournament_id' ] ); 
	
	require_once WP_TOURNREG_DATABASE_PATH . 'scheme.php';
	$scheme = wptournreg_get_field_list();
	
	$fields = preg_split( '/\s*,\s*/', $a[ 'display_fields' ]);
	
	wp_enqueue_script( 'wptournreg' );
	wp_enqueue_style( 'wptournreg' );
	
	/* create name list */
	$names = [];
	$approved = [];
	
	foreach( $result as $participant ) {
		
		if ( strlen( $participant->{ 'lastname' } ) > 0  ) {
			
			if ( strlen( $participant->{ 'firstname' } ) > 0  ) {
				
				$name = $participant->{ 'lastname' } . ', ' . $participant->{ 'firstname' } . ' #' . $participant->{ 'id' };
			}
			else {
				
				$name = $participant->{ 'lastname' } . ' #' . $participant->{ 'id' };
			}
		}
		else if ( strlen( $participant->{ 'firstname' } ) > 0  ) {
			
			$name = $participant->{ 'firstname' } . ' #' . $participant->{ 'id' };
		}
		else {
			
			$name = '#' . $participant->{ 'id' };
		}
				
		$approved[ $name ] = ( $participant->{ 'approved' } ) ? '' : ' class="wptournregedit-not-approved"';
		$names[ $name ] = $participant->{ 'id' };
	}
	ksort( $names );
	
	/* names as HTML selection list */
	$html = '<form><select class="wptournregedit-select">';
	
	foreach( $names as $participant => $id ) {

		$html .= '<option' . $approved[ $participant ] . ' value="#wptournregedit-participant' . esc_attr( $id ) . '">' . esc_html( $participant ) . '</option>';
	}
	
	$html .= '</select></form>';
	
	/* create forms */
	
	$bigsize = 50;
	$smallsize = 15;
	
	$html .= '<div id="wptournregedit-formscontainer">';
	
	foreach( $result as $participant ) {
		
		$html .= '<form id="wptournregedit-participant' . esc_attr( $participant->{ 'id' } ) . '" class="wptournregedit-participant" method="POST" action="' . WP_TOURNREG_ACTION_URL . '"><input type="hidden" name="id" value="' . esc_attr( $participant->{ 'id' } ) . '">';
		
		foreach( $fields as $field ) {
			
			if ( isset( $scheme[ $field ] ) ) {
				
				$html .= '<p><label for="' . esc_attr( $field ) . '"><kbd>' . esc_html( $field ) . '</kbd></label>';
				
				if ( $field == 'id' || $field == 'ip' ) { 
				
					$html .= esc_html( $participant->{ $field } );
				}
				else if ( $field == 'time' ) { 
				
					$html .= wp_date( get_option( 'date_format' ), $participant->{ 'time' } );
				}
				else if ( $field == 'email' ) {
		
					$html .= '<input type="email" name="email" value="' . esc_attr( $participant->{ 'email' } ) . '" size="' . $bigsize . '">';
				}
				else if ( preg_match( '/^phone\d+/i', $scheme[ $field ] ) ) {
					
					$html .= '<input type="tel" value="' . esc_attr( $participant->{ 'email' } ) . '" name="' . esc_attr( $field ) . '" size="' . $mallsize . '">';
				}
				else if ( $scheme[ $field ] == 'text' ) {
					
					$html .= '<textarea cols="' . $bigsize . '" rows="8" name="' . esc_attr( $field ) . '"></textarea>';
				}
				else if ( preg_match( '/char|string|text/i', $scheme[ $field ] ) ) {
					
					$html .= '<input type="text" value="' . esc_attr( $participant->{ $field } ) . '" name="' . esc_attr( $field ) . '" size="' . $bigsize . '">';
				}
				else if ( preg_match( '/bool|int\(1\)/i', $scheme[ $field ] ) ) {
					
					$html .= '<input type="checkbox"' . ( $participant->{ $field } == 1 ? ' checked' : '' ) . ' name="' . esc_attr( $field ) . '">';
				}
				else if ( preg_match( '/int\(/i', $scheme[ $field ] ) ) {
					
					$html .= '<input type="text" value="' . esc_attr( $participant->{ $field } ) .'"' . ' name="' . esc_attr( $field ) . '" size="' . $smallsize . '">';
				}
				else {
					
					return sprintf( __( '%sERROR: Missing format for field %s!%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', '<kbd>' . esc_attr( $field ) . '</kbd>', '</strong>' );
				}
				
				$html .= '</p>';
			}					
		}
		
		/* check three boxes for deletion */
		$count = 0;
		$html .= '<fieldset class="wptournregedit-delete"><legend>' . __( 'Delete' ) . '</legend>';
		
		foreach( [ 'Delete', 'Are you sure?', 'Cannot restore!' ] as $label ) {
			
			$html .= '<input type="checkbox" name="delete' . ++$count . '">';
			$html .= '<span class="wptournregedit-delcheck">' . __( esc_html( $label ), 'wp-tournament-registration' ) . '</span>';
		}
		
		$html .= '</fieldset><input type="hidden" name="action" value="wptournreg_edit_participant"><input type="submit"></form>';
		
	}
	
	$html .= '</div>';
		
	return $html;
}

add_shortcode( 'wptournregedit', 'wptournreg_edit' );
	
/* Action hook of registration form */
function wptournreg_edit_participant() {
	
	global $wpdb;
	
	echo '<html><head></head></html><body><header style="min-height:50px"></header><p>';
	
	if ( isset(  $_POST[ 'delete1' ] ) && isset( $_POST[ 'delete2' ] ) && isset( $_POST[ 'delete3' ] ) ) {
		
		if ( $wpdb->delete( WP_TOURNREG_DATA_TABLE, array( 'id' => $_POST[ 'id' ] ) ) === 1 ) {
		
			_e( 'Entry deleted.', 'wp-tournament-registration');
		}
		else {
			
			printf( __( '%sERROR: Entry not deleted.%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', '</strong>' );		
		}
	}
	else {
			
		require_once WP_TOURNREG_DATABASE_PATH . 'update.php';
		
		if ( wptournreg_update_data() === 1 ) {
		
			_e( 'Entry updated.', 'wp-tournament-registration');
		}
		else {
			
			printf( __( '%sERROR: Entry not updated.%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', '</strong>' );		
		}
	}

	echo '</p>';
	require_once WP_TOURNREG_HTML_PATH . 'backbutton.php';
	echo '</body>';
}
add_action( 'admin_post_nopriv_wptournreg_edit_participant', 'wptournreg_edit_participant' );
add_action( 'admin_post_wptournreg_edit_participant', 'wptournreg_edit_participant' );
