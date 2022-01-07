<?php

// member shortcodes to be used in posts and pages
add_shortcode( 'ocm_competition_group_ranking', 'ocm_sh_competition_group_ranking' );

function ocm_sh_competition_group_ranking( $atts ) {
    
    if ('use_competitions' != get_option('ocm_include_competitions_data')) { 
        return "";
    }
	extract( shortcode_atts( array(
		'compgroup_id' => '',
        'before' => '',
        'after' => '',
	), $atts ) );
    
    $html = "";
    $html .= "<p><table> \n";
    $html .= apply_filters( 'ocm_compgroup_display_ranking', "", $compgroup_id );    
    $html .= "</table></p> \n";
    
	return "$before \n" . $html . "$after \n";
} 
