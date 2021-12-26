<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* stores the form data (no params) */
function wptournreg_get_fields( $atts = [] ) {
	
	require_once WP_TOURNREG_DATABASE_PATH . 'scheme.php';
	$scheme = wptournreg_get_field_list();
	
	global $wpdb;
	
	// normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 
	$a = shortcode_atts( array(
		'checked' => null,
		'class' => null,
		'css' => null,
		'css_id' => null,
		'disabled' => null,
		'field' => null,
		'label' => null,
		'placeholder' => null,
		'required' => null,
	), $atts );
	
	$field = trim( $a[ 'field' ] );
	$checked = ( empty ( $a[ 'checked' ] ) ) ? '' : ' required="checked"';
	$label = '<label for="' . $a[ 'field' ] . '">' . ( empty ( $a[ 'label' ] ) ? $a[ 'field' ] :  $a[ 'label' ]  ) . '</label>';
	$name=' name="' . $a[ 'field' ] . '"';
	$placeholder = ( empty ( $a[ 'placeholder' ] ) ) ? '' : ' placeholder="' . $a[ 'placeholder' ] . '"';
	$required = ( !isset( $a[ 'required' ] ) ) ? '' : ' required';
	$disabled = ( !isset( $a[ 'disabled' ] ) ) ? '' : ' disabled';
	
	/* add custom CSS */
	$css = ( empty ( $a[ 'css' ] ) ) ? '' : ' style="' . trim( esc_attr( $a[ 'css' ] ) ) . '"';
	$class = ' class="wptournreg-field' . ( !isset( $a[ 'required' ] ) ? '' : ' wptourn-required' ) . ( empty ( $a[ 'class' ] ) ? '' : ' ' . trim( esc_attr( $a[ 'class' ] ) ) ) . '"';
	$id = ( empty ( $a[ 'css_id' ] ) ) ? '' : ' id="' . trim( esc_attr( $a[ 'css_id' ] ) ) . '"';
	
	/* sizes */
	$bigsize = 50;
	$smallsize = 15;
	
	if ( $field == 'id' || $field == 'time' ) {
		
		return sprintf( __( '%sERROR: The field value of %s is generated automatically!%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', "<kbd>$field</kbd>", '</strong>' );
	}
	
	if ( !isset( $scheme[ $field ] ) ) {
		
		return sprintf( __( '%sERROR: There is not a field %s!%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', "<kbd>$field</kbd>", '</strong>' );
	}
	else if ( $field == 'email' ) {
		
		return "<p$id$class$css>$label<input$id$class$name$required$disabled$placeholder type='email' size='$bigsize'></p>";
	}
	else if ( preg_match( '/^phone\d+/i', $scheme[ $field ] ) ) {
		
		return "<p$id$class$style>$label<input$id$class$name$required$disabled$placeholder type='tell' size='$mallsize'></p>";
	}
	else if ( $scheme[ $field ] == 'text' ) {
		
		return "<p$id$class$css>$label<textarea$name$required$disabled$placeholder cols='$bigsize' rows='8'></textarea></p>";
	}
	else if ( preg_match( '/char|string|text/i', $scheme[ $field ] ) ) {
	
		return "<p$id$class$css>$label<input$id$class$name$required$disabled$placeholder type='text' size='$bigsize'></p>";
	}
	else if ( preg_match( '/bool|int\(1\)/i', $scheme[ $field ] ) ) {
	
		return "<p$id$class$css>$label<input$id$class$name$checked$disabled type='checkbox'></span>";
	}
	else if ( preg_match( '/int/i', $scheme[ $field ] ) ) {
	
		return "<p$id$class$css>$label<input$id$class$name$required$disabled$placeholder type='text size='$smallsize'></p>";
	}
	else {
		
		return sprintf( __( '%sERROR: Missing format for field %s!%s', 'wp-tournament-registration' ), '<strong class="wptournreg-error">', "<kbd>$field</kbd>", '</strong>' );
	}
}

add_shortcode( 'wptournregfield', 'wptournreg_get_fields' );