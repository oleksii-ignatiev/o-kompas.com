<?php

// custom post type

function ocm_teamplayer_custom_post_init(){
    
    // custom post type: Team Player
    $labels = array(
        'name' => __('Team Players', 'o-kompas'),
        'singular_name' => __('Team Player', 'o-kompas'),
        'add_new' => __('Add New Team Player', 'o-kompas'),
        'add_new_item' => __('Add New Team Player', 'o-kompas'),
        'edit_item' => __('Edit Team Player', 'o-kompas'),
        'new_item' => __('New Team Player', 'o-kompas'),
        'view_item' => __('View Team Player', 'o-kompas'),
        'search_items' => __('Search Team Players', 'o-kompas'),
        'not_found' => __('No Team Players found', 'o-kompas'),
        'not_found_in_trash' => __('No Team Players found in Trash', 'o-kompas'),
        'parent_item_colon' => __('Parent Team Player:', 'o-kompas'),
        'menu_name' => __('Team Players', 'o-kompas'),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array('title','author'/*,'editor'*/),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'orientclub',
        'show_in_admin_bar' => false,
        'menu_position' => 110,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
        'capability_type' => 'page', 
        'map_meta_cap' => true,
        'show_in_rest' => true,
        'rest_base' => 'teamplayers',
    );
    register_post_type('ocm_teamplayer', $args);
}

// hooks and filters for Team Player
add_action('add_meta_boxes', 'ocm_teamplayer_add_fields_box');
add_action('save_post', 'ocm_teamplayer_save_fields');
add_filter('manage_ocm_teamplayer_posts_columns', 'ocm_teamplayer_columns');  
add_filter('manage_edit-ocm_teamplayer_sortable_columns', 'ocm_teamplayer_sortable_column');  
add_action('manage_ocm_teamplayer_posts_custom_column', 'ocm_teamplayer_column_content', 10, 2);   
add_action('rest_api_init', 'ocm_teamplayer_register_rest_fields');

// comp fields operations

function ocm_teamplayer_add_fields_box() {
    global $pagenow, $typenow;
	
    add_meta_box('ocm_teamplayer_fields_box_id', __('Team Player Data', 'o-kompas'), 'ocm_teamplayer_display_fields', 'ocm_teamplayer');
	if ($pagenow == 'post-new.php') {
		add_meta_box('ocm_teamplayer_bulk_box_id', __('Add team players for members in selected category', 'o-kompas'), 'ocm_teamplayer_bulk', 'ocm_teamplayer');
	}
    if ($pagenow == 'post.php') {
		add_meta_box('ocm_teamplayer_list_box_id', __('Team Players', 'o-kompas'), 'ocm_teamplayer_display_list', 'ocm_teamplayer');
	}
}
function ocm_teamplayer_display_fields() {
    global $post;
 
    $values = get_post_custom($post->ID); 

    $member = isset($values['_member']) ? esc_attr($values['_member'][0]) : '';
    $competitorid = isset($values['_competitorid']) ? esc_attr($values['_competitorid'][0]) : '';
    
    wp_nonce_field('ocm_teamplayer_frm_nonce', 'ocm_teamplayer_frm_nonce');
        
    $members = get_posts( array( 'post_type' => 'ocm_member'
                            , 'numberposts' => '-1'
                            , 'meta_key' => '_name'
                            , 'orderby' => 'meta_value'
                            , 'order' => 'ASC'
                            ) );
    $member_options = "<option value=''> ".__('none', 'o-kompas')." </option>\n";

    foreach ($members as $mem) {
        $member_values = get_post_custom($mem->ID);
        $member_options .= "<option " . (($member == $mem->ID) ? "selected='selected' " : "") . "value='$mem->ID'>"
							. sprintf("%s, %s %s", $member_values['_name'][0], $member_values['_firstname'][0], $member_values['_middlename'][0])
                            . "</option>\n";
    }
    
    $teams = get_posts( array( 'post_type' => 'ocm_competitor'
                            , 'numberposts' => '-1'
                            , 'meta_key' => '_competitor_type'
                            , 'meta_value' => 'team'
                            , 'order'=> 'ASC'
                            , 'orderby' => 'title'
                            ) );

    $team_options = "<option value=''> ".__('none', 'o-kompas')." </option>\n";
    foreach ($teams as $team) {
        $team_values = get_post_custom($team->ID);
		$compid = $team_values['_competitionid'][0];
		$comp_values = get_post_custom($compid);
		$groupid = (($compid != "") ? $comp_values['_groupid'][0] : "");
        $team_options .= "<option " . (($competitorid == $team->ID) ? "selected='selected' " : "") . "value='$team->ID'>"  
						. sprintf("%s - [%s%s]", $team_values['_name'][0], get_the_title( $compid ), (($groupid != "") ? (" - " . get_the_title( $groupid )) : "") )  
						. "</option>\n";
    }

    
    $html = "
        <tr> 
        <td align='right'><label>".__('Member', 'o-kompas')."</label></td>
        <td><select name='member'>\n". $member_options ."</select></td>
        </tr>
        <tr>
        <td align='right'><label>".__('Team', 'o-kompas')."</label></td>
        <td><select id='competitorid'>\n". $team_options ."</select></td>
        </tr>
        ";
    echo "<table>" . $html . "</table>";

}
function ocm_teamplayer_bulk() {
	
	$description = array( __('Define entries', 'o-kompas')
						, __('View and select entries for creation', 'o-kompas')
						, __('Confirm', 'o-kompas')
						, __('Creating entries', 'o-kompas')
						);
						
	$html_step1 = "
		<p>
		<table>
		<tr>
		<td><label>".__('Member Club', 'o-kompas')."</label></td>
		<td>
		".wp_dropdown_categories(array(
					'show_option_none' => __('select club from list...', 'o-kompas'),
					'taxonomy' => 'ocm_member_category',
					'id' => 'bulk_member_category',
					'orderby' => 'name',
					'show_count' => true,
					'hide_empty' => false,
					'hierarchical' => 1, 
					'depth' => 4,
					'echo' => 0
			  ))
		."
		</td>
		</tr>
		</table>
		</p>";
	
	echo ocm_bulk_import_wizard($description, $html_step1);
}
function ocm_teamplayer_display_list() {
    global $post;
 
    $values = get_post_custom($post->ID); 

    $competitorid = isset($values['_competitorid']) ? esc_attr($values['_competitorid'][0]) : '';
        
    $players = get_posts( array( 'post_type' => 'ocm_teamplayer'
                                , 'numberposts' => '-1'
                                , 'meta_key' => '_competitorid'
                                , 'meta_value' => $competitorid
                                , 'orderby' => 'meta_value'
                                , 'order' => 'ASC'
                                ) );
    foreach ( $players as $player ) {
        $member_values = get_post_custom($player->ID);
        echo sprintf("<p><a href=%s target=\"_blank\">%s</a></p>"
                    , site_url( "/wp-admin/post.php?post=".$player->ID."&action=edit" )
                    , get_the_title( $member_values['_member'][0] ));
    }    

}

function ocm_teamplayer_save_fields($post_id) {
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

        // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['ocm_teamplayer_frm_nonce']) || !wp_verify_nonce($_POST['ocm_teamplayer_frm_nonce'], 'ocm_teamplayer_frm_nonce'))
        return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_posts'))
        return;

    if (isset($_POST['member']))
        update_post_meta($post_id, '_member', esc_attr($_POST['member']));
    if (isset($_POST['competitorid']))
        update_post_meta($post_id, '_competitorid', esc_attr($_POST['competitorid']));
}

// display comp data on admin page

function ocm_teamplayer_columns($defaults) {  
    // no author, (post)date
    unset($defaults['author']);  
    unset($defaults['date']);  
    
    // new column titles
    $defaults['member']  = __('Member', 'o-kompas');
    $defaults['team']  = __('Team', 'o-kompas');
    $defaults['competition']  = __('Competition', 'o-kompas');
    
    return $defaults;  
}  
  
function ocm_teamplayer_sortable_column( $columns ) {  
    $columns['member'] = 'member';  
    $columns['team'] = 'team';  
    $columns['competition'] = 'competition';  
    
    //To make a column 'un-sortable' remove it from the array  
    //unset($columns['date']);  
  
    return $columns;  
}   
  
function ocm_teamplayer_column_content($column_name, $post_ID) {  
    switch($column_name){
        case 'member':
            $id = get_post_meta( $post_ID , '_member' , true );
            $member = get_post_custom($id);
            echo sprintf("%s, %s %s", $member['_name'][0], $member['_firstname'][0], $member['_middlename'][0] );
            return;
        case 'team':
            $id = get_post_meta( $post_ID , '_competitorid' , true );
            $team = get_post_custom($id);
            echo sprintf("%s", $team['_name'][0] );
            return;
        case 'competition':
            $id = get_post_meta( $post_ID , '_competitorid' , true );
            $team = get_post_custom($id);
            echo sprintf("%s", get_the_title( $team['_competitionid'][0] ) );
            return;
        default:
            echo print_r($post_ID,true); //Show the whole array for troubleshooting purposes
            return;
    }
} 

// rest api
function ocm_teamplayer_register_rest_fields(){
	register_rest_field( 'ocm_teamplayer', 'member_id', array( /*'get_callback' => 'ocm_member_get_middle_name' 
															 , */'update_callback' => 'ocm_teamplayer_update' 
															 , 'schema' => array( 'description' => 'ocm_member post id' , 'type' => 'integer', 'required' => 'true' )) ); 
	register_rest_field( 'ocm_teamplayer', 'competitor_id', array( 'update_callback' => 'ocm_teamplayer_update' 
																  , 'schema' => array( 'description' => 'ocm_competitor post id' , 'type' => 'integer', 'required' => 'true' )) ); 
}
//function ocm_member_get_name($post) { return get_post_meta( $post['id'], '_name', true ); }
//function ocm_member_get_middle_name($post) { return get_post_meta( $post['id'], '_middlename', true ); }
//function ocm_member_get_first_name($post) { return get_post_meta( $post['id'], '_firstname', true ); }

function ocm_teamplayer_update($value, $post, $key) { 
	switch ( $key ) {
		case 'member_id' 		:  update_post_meta( $post->ID, '_member', $value ); break;
		case 'competitor_id' 	:  update_post_meta( $post->ID, '_competitorid', $value ); break;
		default : return false;
	}
	return true; 
}
