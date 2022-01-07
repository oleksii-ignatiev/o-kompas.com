<?php 

define('OCM_CFMT_NONE', 1000);             

add_filter('ocm_competition_get_formats', 'ocm_comp_format_none_get_format', 10, 2 );
add_filter('ocm_competition_display_format', 'ocm_comp_format_none_display_format', 10, 2 );
add_filter('ocm_competition_display_ranking', 'ocm_comp_format_none_display_ranking', 10, 4 );
                   
function ocm_comp_format_none_get_format( $html, $id ) {
    $html .= "<option ";      
    $html .= ($id == OCM_CFMT_NONE) ? "selected='selected' " : "";   
    $html .= "value=" . OCM_CFMT_NONE. ">" . __('none', TEXT_DOMAIN) . "</option>\n";

    return $html; 
}

function ocm_comp_format_none_display_format( $html, $compid ) {
     
    if ( get_post_meta( $compid, '_formatid', true) == OCM_CFMT_NONE ) {
        $html .= __('none', TEXT_DOMAIN); 
    }
        
    return $html; 
}

function ocm_comp_format_none_display_ranking( $html, $compid, $display_all, $display_header ) {
   
    if ( get_post_meta( $compid, '_formatid', true) == OCM_CFMT_NONE ) {
        $html .= __('no ranking', TEXT_DOMAIN); 
    }
        
    return $html; 
}
