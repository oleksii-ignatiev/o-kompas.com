<?php

// include pre-installed competition formats
include_once 'competitionformat_none.php';
include_once 'competitionformat_league.php';
include_once 'competitionformat_knockout.php';
include_once 'competitionformat_individual.php';
include_once 'competitionformat_ladder.php';

// custom post type

function ocm_comp_custom_post_init(){
    
    // taxonomy: Competition Category
    $labels = array(
        'name'              => __('Competition Categories', 'o-kompas'),
        'singular_name'     => __('Competition Category', 'o-kompas'),
        'search_items'      => __('Search Competition Categories', 'o-kompas'),
        'all_items'         => __('All Competition Categories', 'o-kompas'),
        'parent_item'       => __('Parent Competition Category', 'o-kompas'),
        'parent_item_colon' => __('Parent Competition Category:', 'o-kompas'),
        'edit_item'         => __('Edit Competition Category', 'o-kompas'), 
        'update_item'       => __('Update Competition Category', 'o-kompas'),
        'add_new_item'      => __('Add New Competition Category', 'o-kompas'),
        'new_item_name'     => __('New Competition Category', 'o-kompas'),
        'menu_name'         => __('Competition Categories', 'o-kompas'),
    ); 	
    register_taxonomy('ocm_comp_category',array('ocm_comp'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'ocm_comp_category' ),
        'show_in_rest' => true,
        'rest_base' => 'competition_categories',
    ));

    // custom post type: Competition
    $labels = array(
        'name' => __('Competitions', 'o-kompas'),
        'singular_name' => __('Competition', 'o-kompas'),
        'add_new' => __('Add New Competition', 'o-kompas'),
        'add_new_item' => __('Add New Competition', 'o-kompas'),
        'edit_item' => __('Edit Competition', 'o-kompas'),
        'new_item' => __('New Competition', 'o-kompas'),
        'view_item' => __('View Competition', 'o-kompas'),
        'search_items' => __('Search Competitions', 'o-kompas'),
        'not_found' => __('No Competitions found', 'o-kompas'),
        'not_found_in_trash' => __('No Competitions found in Trash', 'o-kompas'),
        'parent_item_colon' => __('Parent Competition:', 'o-kompas'),
        'menu_name' => __('Competitions', 'o-kompas'),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array('title','author','editor','thumbnail'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'orientclub',
        'show_in_admin_bar' => true,
        'menu_position' => 110,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'taxonomies' => array( 'ocm_comp_category' ),
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
        'capability_type' => 'page', 
        'map_meta_cap' => true,
        'show_in_rest' => true,
        'rest_base' => 'competitions',
    );
    register_post_type('ocm_comp', $args);
}

// hooks and filters for Competition
add_action('add_meta_boxes', 'ocm_comp_add_fields_box');
add_action('save_post', 'ocm_comp_save_fields');
add_filter('manage_ocm_comp_posts_columns', 'ocm_comp_columns');  
add_filter('manage_edit-ocm_comp_sortable_columns', 'ocm_comp_sortable_column');  
add_action('manage_ocm_comp_posts_custom_column', 'ocm_comp_column_content', 10, 2);   
add_action('restrict_manage_posts', 'ocm_comp_filtered_by_category');
add_filter('parse_query', 'ocm_comp_category_from_id_in_query');

// comp fields operations

function ocm_comp_add_fields_box() {
    global $pagenow, $typenow;
	
    add_meta_box('ocm_comp_fields_box_id', __('Competition Data', 'o-kompas'), 'ocm_comp_display_fields', 'ocm_comp');
    if ($pagenow == 'post.php') {
		add_meta_box('ocm_comp_ranking_id', __('Ranking', 'o-kompas'), 'ocm_comp_display_ranking', 'ocm_comp');
		add_meta_box('ocm_comp_matches_id', __('Matches', 'o-kompas'), 'ocm_comp_display_matches', 'ocm_comp');
        add_meta_box('ocm_comp_shortcodes_box_id', __('Shortcodes', 'o-kompas'), 'ocm_comp_list_shortcodes', 'ocm_comp', 'side', 'high');
	}
}
function ocm_comp_display_fields() {
    global $post;
 
    $values = get_post_custom($post->ID);
    
    $formatid = isset($values['_formatid']) ? esc_attr($values['_formatid'][0]) : oCM_CFMT_NONE;
    $groupid  = isset($values['_groupid']) ? esc_attr($values['_groupid'][0]) : '';
    
    wp_nonce_field('ocm_comp_frm_nonce', 'ocm_comp_frm_nonce');
    
    $groups = get_posts( array( 'post_type' => 'ocm_comp_group'
                            , 'numberposts' => '-1'
                            , 'order'=> 'ASC'
                            , 'orderby' => 'title'
                            ) );

    $group_options = "<option value=''> ".__('none', 'o-kompas')." </option>\n";
    foreach ($groups as $group) {
        $group_options .= "<option ";  
        $group_options .= ($groupid == $group->ID) ? "selected='selected' " : "";           
        $group_options .= "value='$group->ID'>" . get_the_title($group->ID) . "</option>\n";
    }
    
    $html =  "<tr>\n"
            ."<td align='right'><label>".__('Competition format', 'o-kompas')."</label></td>\n"
            ."<td><select name='formatid'>\n". apply_filters( 'ocm_competition_get_formats', "", $formatid ) ."</select></td>\n"
            ."</tr>\n"
            . apply_filters( 'ocm_competition_display_format_fields', "", $post->ID )
            ."<tr>\n"
            ."<td align='right'><label>".__('Competition group', 'o-kompas')."</label></td>\n"
            ."<td><select name='groupid'>\n". $group_options ."</select></td>\n"
            ."</tr>\n";
    echo "<table>\n" . $html . "</table>\n";
}
function ocm_comp_display_ranking() {
    global $post;
 
    echo "[<a href='".admin_url( '/post-new.php?post_type=ocm_competitor', __FILE__ )."'>".__('Add New Competitors', 'o-kompas')."</a>]";    
    echo "<table>" . apply_filters( 'ocm_competition_display_ranking', "", $post->ID, true, true ) . "</table>";    
}
function ocm_comp_display_matches() {
    global $post;
 
    $matches = get_posts( array( 'post_type' => 'ocm_match'
                                , 'numberposts' => '-1'
								, 'meta_query' => array(
											array(
												'key'   => '_competitionid',
												'value' => $post->ID
											)
								  )
                                , 'meta_key' => '_date'
								, 'meta_type' => 'DATE'
                                , 'orderby'  => 'meta_value'
								, 'order' => 'ASC'
                                ) );
	
    echo "[<a href='".admin_url( '/post-new.php?post_type=ocm_match', __FILE__ )."'>".__('Add New Matches', 'o-kompas')."</a>]";    
    echo "<table>";
    foreach ( $matches as $match ) {
		$compid_1 = get_post_meta( $match->ID , '_compid_1' , true );
        $compid_2 = get_post_meta( $match->ID , '_compid_2' , true );
		$result   = get_post_meta( $match->ID , '_result' , true );
		$resultstring = ( chop($result,":") == '' ? '' : apply_filters( 'ocm_match_display_result', $result, $post->ID ) );

        echo sprintf("<tr>");
        echo apply_filters( 'ocm_comp_display_match_meta', sprintf("<td>%s (%s)</td>", get_post_meta( $match->ID , '_date' , true ), get_post_meta( $match->ID , '_time' , true )), $match->ID, $post->ID);
        echo sprintf("<td><a href=%s target=\"_blank\">%s". apply_filters( 'ocm_match_display_single_competitor_field', " - %s", $post->ID) ."</a></td>"
                    , site_url( "/wp-admin/post.php?post=".$match->ID."&action=edit" )
                    , apply_filters( 'ocm_comp_display_match_competitor_1', get_the_title( $compid_1 ), $match->ID, $matches, $post->ID )
                    , apply_filters( 'ocm_comp_display_match_competitor_2', get_the_title( $compid_2 ), $match->ID, $matches, $post->ID ) 
					);
        echo sprintf("<td>%s</td>", $resultstring);
        echo sprintf("</tr>");
    }    
    echo "</table>";
}
function ocm_comp_list_shortcodes() {
	global $post;
	
	$html = __('Use the following shortcodes by copy/pasting into a post or page (see documentation).', 'o-kompas');

	$html .= sprintf("<p>[ocm_competition_ranking competition_id='%s']</p>", $post->ID);
	$html .= sprintf("<p>[ocm_match_data competition_id='%s']</p>", $post->ID);
	
	echo $html;
}

function ocm_comp_save_fields($post_id) {
    global $post;

    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['ocm_comp_frm_nonce']) || !wp_verify_nonce($_POST['ocm_comp_frm_nonce'], 'ocm_comp_frm_nonce'))
        return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_posts'))
        return;

    do_action( 'ocm_competition_save_format_fields', $post_id );     
    
    $values = get_post_custom($post->ID);
    $formatid = isset($values['_formatid']) ? esc_attr($values['_formatid'][0]) : '';
    $formatok = isset($values['_formatok']) ? esc_attr($values['_formatok'][0]) : 'false';
    
    if (($formatid == $_POST['formatid']) && ($formatok == 'false')) {  
            
        // remove action to prevent infinite loop as create_matches() creates/saves posts
        remove_action('save_post', 'ocm_comp_save_fields');
        
        do_action( 'ocm_competition_create_matches', $post_id );  // for KnockOut only - to be removed later
        
        // reassign action again
        add_action('save_post', 'ocm_comp_save_fields');
        
        update_post_meta($post_id, '_formatok', 'true'); 
    } else {
        if ($formatid != $_POST['formatid'])
            update_post_meta($post_id, '_formatok', 'false'); 
    }
    
    if (isset($_POST['formatid']))
        update_post_meta($post_id, '_formatid', esc_attr($_POST['formatid']));
    if (isset($_POST['groupid']))
        update_post_meta($post_id, '_groupid', esc_attr($_POST['groupid']));  
        
}

// display comp data on admin page

function ocm_comp_columns($defaults) {  
    // no author
    unset($defaults['author']);  
    
    // new column titles
    $defaults['group']  = __('Competition group', 'o-kompas');
    $defaults['format']  = __('Competition format', 'o-kompas');
    $defaults['category']  = __('Category', 'o-kompas');
    
    return $defaults;  
}  
  
function ocm_comp_sortable_column( $columns ) {  
    $columns['group'] = 'group';  
    $columns['format'] = 'format';  
    
    //To make a column 'un-sortable' remove it from the array  
    //unset($columns['date']);  
  
    return $columns;  
}   
  
function ocm_comp_column_content($column_name, $post_ID) {  
    switch($column_name){
        case 'group':
            $group_ID = get_post_meta( $post_ID , '_groupid' , true );
            if ( $group_ID != '' ) {
                echo sprintf("%s", get_the_title( $group_ID ));
            } 
            return;
        case 'format':
            echo apply_filters( 'ocm_competition_display_format', "", $post_ID );            
            return;
        case 'category':
            $categories = wp_get_post_terms( $post_ID, 'ocm_comp_category', array("fields" => "names") );
            echo sprintf("%s",  implode(", ", $categories) );
            return;
        default:
            echo print_r($post_ID,true); //Show the whole array for troubleshooting purposes
            return;
    }
} 

// filter comps by category

function ocm_comp_filtered_by_category() {
    global $typenow;
    $post_type = 'ocm_comp'; 
    $taxonomy = 'ocm_comp_category'; 
    if ($typenow == $post_type) {
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' => __("Show All {$info_taxonomy->label}", 'o-kompas'),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'show_count' => true,
            'hide_empty' => false,
            'hierarchical' => 1, 
            'depth' => 4,
	        ));
    };
}

function ocm_comp_category_from_id_in_query($query) {
    global $pagenow;
    $post_type = 'ocm_comp'; 
    $taxonomy = 'ocm_comp_category'; 
    $q_vars = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type ) {
    
        // if not filtering on member category, exclude (optional) categories from member list
        if ( !isset($q_vars[$taxonomy]) ) {
            $q_vars['tax_query'] = array(
                                        array(
                                            'taxonomy' => $taxonomy,
                                            'field' => 'id',
                                            'terms' => array( apply_filters('ocm_comp_exclude_categories', "") ),      
                                            'operator' => 'NOT IN'
                                        )
                                   );
        }
        else  if ( is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }
}

// generate list of competition matches
function ocm_competition_list( $category ) {

	if ($category == -1) {
		$competitions = get_posts( array( 'post_type' => 'ocm_comp'
										, 'numberposts' => '-1'
										) );
	} else {								
		$competitions = get_posts( array( 'post_type' => 'ocm_comp'
										, 'tax_query' => array(
											array(
												'taxonomy' => 'ocm_comp_category',
												'field' => 'id',
												'terms' => array( $category )
											)
										  )                            
										, 'numberposts' => '-1'
										) );
	}

    echo "sep=,\n";    
    foreach ( $competitions as $competition ) {
		ocm_competition_list_header($competition->ID);
		echo "\n";
        ocm_matches_list($competition->ID);
		echo "\n";		
    }
}

function ocm_competition_list_header( $comp_id ) {

    $names = array( __("Competition Group", 'o-kompas'), __("Competition", 'o-kompas'),
					__("Categories", 'o-kompas')
                  );
    echo '"' . join( $names, '","' ) . '",' ;
	ocm_match_list_header( $comp_id );
}

