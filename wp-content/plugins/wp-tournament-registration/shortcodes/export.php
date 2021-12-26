<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* returns a customizable txt file */
function wptournreg_export( $atts = [], $content = null ) {
	
	// normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
	
	$a = shortcode_atts( array(
		'all' => null,
		'class' => null,
		'css' => null,
		'css_id' => null,
		'fields_set' => null,
		'filename' => 'wptournreg.txt',
		'format' => null,
		'linebreak' => '',
		'tournament_id' => null,
	), $atts );
	
	wp_enqueue_script( 'wptournreg' );
	wp_enqueue_style( 'wptournreg' );
	
	$all = ( !empty ( $a[ 'all' ] ) ) ? "<input type='hidden' name='all' value='1'>"  : '';
	
	/* error if tournament id is missing */
	if ( empty ( $a[ 'tournament_id' ] ) ) {
		
		return sprintf( __( '%sERROR: Missing %s in shortcode %s!%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', '<kbd>tournament_id</kbd>', '<kbd>wptournregform</kbd>', '</strong>' );
	}
	else {
		$tournament = '<input type="hidden" name="tournament_id" value="' . $a[ 'tournament_id' ] . '">';
	}
	
	/* add custom CSS */
	$css = ( empty ( $a[ 'css' ] ) ) ? '' : ' style="' . trim( esc_attr( $a[ 'css' ] ) ) . '"';
	$class = ' class="wptournreg-txt' . ( empty ( $a[ 'class' ] ) ? '' :  ' ' . trim( esc_attr( $a[ 'class' ] ) ) ) . '"';
	$id = ( empty ( $a[ 'css_id' ] ) ) ? '' : ' id="' . trim( esc_attr( $a[ 'css_id' ] ) ) . '"';
	
	/* txt structure */
	$format = "<input type='hidden' name='format' value='" . esc_attr( $a[ 'format' ] ) . "'>";
	$linebreak = '<input type="hidden" name="linebreak" value="' . esc_attr( $a[ 'linebreak' ] ) . '">';
	$fields_set = "<input type='hidden' name='fields_set' value='" . esc_attr( $a[ 'fields_set' ] ) . "'>";
	$filename= '<input type="hidden" name="filename" value="' . esc_attr( $a[ 'filename' ] ) . '">';
	
	/* set action URL */
	$action = ' method="POST" action="' . WP_TOURNREG_ACTION_URL . '"';

	return "<form$id$class$css$action target='_blank'><p><strong>$content</strong></p>$tournament$format$linebreak$fields_set$filename$all" . '<input type="hidden" name="action" value="wptournreg_get_txt"><input type="submit"></form>';
	
}

add_shortcode( 'wptournregexport', 'wptournreg_export' );

/* Action hook of txt form */
function wptournreg_get_txt() {
	
	require_once WP_TOURNREG_DATABASE_PATH . 'select.php';
	$result = wptournreg_select_tournament( $_POST[ 'tournament_id' ] ); 
	
	$formatted = [];
	
	$linebreak = ( !isset( $_POST[ 'linebreak' ]  ) || empty ( $_POST[ 'linebreak' ] ) ) ? '' : "\n";
	
	$fields_set = preg_split( '/\s*,\s*/', $_POST[ 'fields_set' ] );
	
	foreach( $result as $participant ) {
		
		if ( $participant->{ 'approved' } || isset( $_POST[ 'all' ] ) ) {
		
			$found = true;
			
			if ( !empty( $_POST[ 'fields_set' ] ) ) {
				
				foreach( $fields_set as $available ) {
					
					if ( !isset( $participant->{ $available } ) || empty ( $participant->{ $available } ) ) {
						
						$found = false;
						break;  
					}
				}
			}
			
			if ( $found ) {
			
				$row = sanitize_textarea_field( $_POST[ 'format' ] );
				
				foreach( $participant as $field => $value ) {
					
					$row = str_replace( '§' . $field . '§', esc_html( $value ), $row );
					$row = str_replace( '\"', '"', $row );
					$row = str_replace( "\'", "'", $row );
					$row = str_replace( 'LOWER_THAN', "<", $row );
				}
				
				$formatted[] = $row;
			}
		}
	}
	
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename="' . $_POST[ 'filename' ] . '"');

	echo implode( $linebreak, $formatted );
}
add_action( 'admin_post_nopriv_wptournreg_get_txt', 'wptournreg_get_txt' );
add_action( 'admin_post_wptournreg_get_txt', 'wptournreg_get_txt' );