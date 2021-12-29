<?php

// include pre-installed competition group formats
include_once 'compgroupformat_list.php';
include_once 'compgroupformat_individual.php';

// custom post type

function ocm_comp_group_custom_post_init(){
    
    // taxonomy: Competition Group Category
    $labels = array(
        'name' => __('Competition Group Categories', 'o-kompas'),
        'singular_name' => __('Competition Group Category', 'o-kompas'),
        'search_items' => __('Search Competition Group Categories', 'o-kompas'),
        'all_items' => __('All Competition Group Categories', 'o-kompas'),
        'parent_item' => __('Parent Competition Group Category', 'o-kompas'),
        'parent_item_colon' => __('Parent Competition Group Category:', 'o-kompas'),
        'edit_item' => __('Edit Competition Group Category', 'o-kompas'), 
        'update_item' => __('Update Competition Group Category', 'o-kompas'),
        'add_new_item' => __('Add New Competition Group Category', 'o-kompas'),
        'new_item_name' => __('New Competition Group Category', 'o-kompas'),
        'menu_name' => __('Competition Group Categories', 'o-kompas'),
    ); 	
    register_taxonomy('ocm_comp_group_category',array('ocm_comp_group'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'ocm_comp_group_category' ),
        'show_in_rest' => true,
        'rest_base' => 'compgroup_categories',
    ));

    // custom post type: Competition Group
    $labels = array(
        'name' => __('Competition Groups', 'o-kompas'),
        'singular_name' => __('Competition Group', 'o-kompas'),
        'add_new' => __('Add New Competition Group', 'o-kompas'),
        'add_new_item' => __('Add New Competition Group', 'o-kompas'),
        'edit_item' => __('Edit Competition Group', 'o-kompas'),
        'new_item' => __('New Competition Group', 'o-kompas'),
        'view_item' => __('View Competition Group', 'o-kompas'),
        'search_items' => __('Search Competition Groups', 'o-kompas'),
        'not_found' => __('No Competition Groups found', 'o-kompas'),
        'not_found_in_trash' => __('No Competition Groups found in Trash', 'o-kompas'),
        'parent_item_colon' => __('Parent Competition Group:', 'o-kompas'),
        'menu_name' => __('Competition Groups', 'o-kompas'),
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
        'taxonomies' => array( 'ocm_comp_group_category' ),
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
        'capability_type' => 'page', 
        'map_meta_cap' => true,
        'show_in_rest' => true,
        'rest_base' => 'compgroups',
    );
    register_post_type('ocm_comp_group', $args);
}

// hooks and filters for Competition Group
add_action('add_meta_boxes', 'ocm_comp_group_add_fields_box');
add_action('save_post', 'ocm_comp_group_save_fields');
add_filter('manage_ocm_comp_group_posts_columns', 'ocm_comp_group_columns');  
add_filter('manage_edit-ocm_comp_group_sortable_columns', 'ocm_comp_group_sortable_column');  
add_action('manage_ocm_comp_group_posts_custom_column', 'ocm_comp_group_column_content', 10, 2);   
add_action('restrict_manage_posts', 'ocm_comp_group_filtered_by_category');
add_filter('parse_query', 'ocm_comp_group_category_from_id_in_query');

// comp fields operations

function ocm_comp_group_add_fields_box() {
    global $pagenow, $typenow;
	
    add_meta_box('ocm_comp_group_fields_box_id', __('Competition Group Data', 'o-kompas'), 'ocm_comp_group_display_fields', 'ocm_comp_group');
    if ($pagenow == 'post.php') {
		add_meta_box('ocm_comp_group_ranking_id', __('Ranking', 'o-kompas'), 'ocm_comp_group_display_ranking', 'ocm_comp_group');
		add_meta_box('ocm_comp_group_list_id', __('Competitions', 'o-kompas'), 'ocm_comp_group_display_list', 'ocm_comp_group');
        add_meta_box('ocm_comp_group_shortcodes_box_id', __('Shortcodes', 'o-kompas'), 'ocm_comp_group_list_shortcodes', 'ocm_comp_group', 'side', 'high');
	}
}
function ocm_comp_group_display_fields() {
    global $post;
 
    $values = get_post_custom($post->ID);
    
    $format = isset($values['_format']) ? esc_attr($values['_format'][0]) : __('none', 'o-kompas');
    $groupid = isset($values['_groupid']) ? esc_attr($values['_groupid'][0]) : '';
    
    wp_nonce_field('ocm_comp_group_frm_nonce', 'ocm_comp_group_frm_nonce');
    
    $html = "
        <tr>
        <td align='right'><label>".__('Group Format', 'o-kompas')."</label></td>\n
        <td><select name='format'>\n". apply_filters( 'ocm_compgroup_get_formats', "", $format ) ."</select></td>\n
        </tr>
        ";
    echo "<table>" . $html . "</table>";
}
function ocm_comp_group_display_ranking() {
    global $post;
 
    echo "<table>" . apply_filters( 'ocm_compgroup_display_ranking', "", $post->ID ) . "</table>";    
}
function ocm_comp_group_display_list() {
    global $post;
 
    $competitions = get_posts( array( 'post_type' => 'ocm_comp'
                                , 'numberposts' => '-1'
                                , 'meta_key' => '_groupid'
                                , 'meta_value' => $post->ID
                                , 'orderby' => 'meta_value'
                                , 'order' => 'ASC'
                                ) );
    
    foreach ( $competitions as $comp ) {
        echo sprintf("<p><a href=%s target=\"_blank\">%s</a></p>"
                    , site_url( "/wp-admin/post.php?post=".$comp->ID."&action=edit" )
                    , get_the_title( $comp->ID ));
    }    

}
function ocm_comp_group_list_shortcodes() {
	global $post;
	
	$html = __('Use the following shortcodes by copy/pasting into a post or page (see documentation).', 'o-kompas');

	$html .= sprintf("<p>[ocm_competition_group_ranking compgroup_id='%s']</p>", $post->ID);
	
	echo $html;
}

function ocm_comp_group_save_fields($post_id) {
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

        // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['ocm_comp_group_frm_nonce']) || !wp_verify_nonce($_POST['ocm_comp_group_frm_nonce'], 'ocm_comp_group_frm_nonce'))
        return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_posts'))
        return;

    if (isset($_POST['format']))
        update_post_meta($post_id, '_format', esc_attr($_POST['format']));
}

// display comp data on admin page

function ocm_comp_group_columns($defaults) {  
    // no author
    unset($defaults['author']);  
    
    // new column titles
    $defaults['format'] = __('Group Format', 'o-kompas');
    $defaults['category'] = __('Category', 'o-kompas');
    
    return $defaults;  
}  
  
function ocm_comp_group_sortable_column( $columns ) {  
    $columns['format'] = 'format';  
    
    //To make a column 'un-sortable' remove it from the array  
    //unset($columns['date']);  
  
    return $columns;  
}   
  
function ocm_comp_group_column_content($column_name, $post_ID) {  
    switch($column_name){
        case 'format':
            echo apply_filters( 'ocm_compgroup_display_format', "", $post_ID );            
            return;
        case 'category':
            $categories = wp_get_post_terms( $post_ID, 'ocm_comp_group_category', array("fields" => "names") );
            echo sprintf("%s",  implode(", ", $categories) );
            return;
        default:
            echo print_r($post_ID,true); //Show the whole array for troubleshooting purposes
            return;
    }
} 

// filter comps by category

function ocm_comp_group_filtered_by_category() {
    global $typenow;
    $post_type = 'ocm_comp_group'; 
    $taxonomy = 'ocm_comp_group_category'; 
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

function ocm_comp_group_category_from_id_in_query($query) {
    global $pagenow;
    $post_type = 'ocm_comp_group'; 
    $taxonomy = 'ocm_comp_group_category'; 
    $q_vars = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type ) {
    
        // if not filtering on member category, exclude (optional) categories from member list
        if ( !isset($q_vars[$taxonomy]) ) {
            $cat_excluded = get_option('ocm_cat_excluded_default_comp');
            $q_vars['tax_query'] = array(
                                        array(
                                            'taxonomy' => $taxonomy,
                                            'field' => 'id',
                                            'terms' => array($cat_excluded),      
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

