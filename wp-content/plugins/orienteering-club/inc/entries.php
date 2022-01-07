<?php

// custom post type

function ocm_entry_custom_post_init(){
    
    // custom post type: Entry
    $labels = array(
        'name' => __('Entries', TEXT_DOMAIN),
        'singular_name' => __('Entry', TEXT_DOMAIN),
        'add_new' => __('Add New Entry', TEXT_DOMAIN),
        'add_new_item' => __('Add New Entry', TEXT_DOMAIN),
        'edit_item' => __('Edit Entry', TEXT_DOMAIN),
        'new_item' => __('New Entry', TEXT_DOMAIN),
        'view_item' => __('View Entry', TEXT_DOMAIN),
        'search_items' => __('Search Entries', TEXT_DOMAIN),
        'not_found' => __('No Entries found', TEXT_DOMAIN),
        'not_found_in_trash' => __('No Entries found in Trash', TEXT_DOMAIN),
        'parent_item_colon' => __('Parent Entry:', TEXT_DOMAIN),
        'menu_name' => __('Entries', TEXT_DOMAIN),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array('title'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'orientclub',
        'show_in_admin_bar' => true,
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
        'rest_base' => 'entries',
    );
    register_post_type('ocm_entry', $args);
}

// hooks and filters for entry
add_action('add_meta_boxes', 'ocm_entry_add_fields_box');
add_action('save_post', 'ocm_entry_save_fields');
add_filter('manage_ocm_entry_posts_columns', 'ocm_entry_columns');  
add_filter('manage_edit-ocm_entry_sortable_columns', 'ocm_entry_sortable_column');  
add_action('manage_ocm_entry_posts_custom_column', 'ocm_entry_column_content', 10, 2);   
add_action('rest_api_init', 'ocm_entry_register_rest_fields');

// comp fields operations

function ocm_entry_add_fields_box() {
    global $pagenow, $typenow;
	
    add_meta_box('ocm_entry_fields_box_id', __('entry Data', TEXT_DOMAIN), 'ocm_entry_display_fields', 'ocm_entry');
	if ($pagenow == 'post-new.php') {
		add_meta_box('ocm_entry_bulk_box_id', __('Add entries for members in selected category', TEXT_DOMAIN), 'ocm_entry_bulk', 'ocm_entry');
	}
    if ($pagenow == 'post.php') {
		add_meta_box('ocm_teamplayer_list_box_id', __('Team Players', TEXT_DOMAIN), 'ocm_entry_display_team', 'ocm_entry');
        add_meta_box('ocm_entry_shortcodes_box_id', __('Shortcodes', TEXT_DOMAIN), 'ocm_entry_list_shortcodes', 'ocm_entry', 'side', 'high');
	}
}
function ocm_entry_display_fields() {
    global $post;
 
    $values = get_post_custom($post->ID); 

    $name = $teamname = isset($values['_name']) ? esc_attr($values['_name'][0]) : '';
    $member = isset($values['_member']) ? esc_attr($values['_member'][0]) : '';
    $entry_type = isset($values['_entry_type']) ? esc_attr($values['_entry_type'][0]) : '';
    $member_checked = $external_checked = $team_checked = "";
    if ($entry_type == 'member') {
        $member_checked = "checked";
    } else if ($entry_type == 'external') {
        $external_checked = "checked";
        $teamname = '';
    } else if ($entry_type == 'team') {
        $team_checked = "checked";
        $name = '';
    }
    $disqualified = isset($values['_disqualified']) ? esc_attr($values['_disqualified'][0]) : '';
    if ($disqualified == 'yes') {
        $disqualified_checked = "checked";
    } else {
        $disqualified_checked = "";
    }
    $competitionid = isset($values['_competitionid']) ? esc_attr($values['_competitionid'][0]) : '';
    
    wp_nonce_field('ocm_entry_frm_nonce', 'ocm_entry_frm_nonce');
        
    $members = get_posts( array( 'post_type' => 'ocm_member'
                            , 'numberposts' => '-1'
                            , 'meta_key' => '_name'
                            , 'orderby' => 'meta_value'
                            , 'order' => 'ASC'
                            ) );
    $member_options = "<option value=''> ".__('none', TEXT_DOMAIN)." </option>\n";

    foreach ($members as $mem) {
        $member_values = get_post_custom($mem->ID);
        $member_options .= "<option " . (($member == $mem->ID) ? "selected='selected' " : "") . "value='$mem->ID'>" 
                          . $member_values['_name'][0] . ", " . $member_values['_firstname'][0] . " " . $member_values['_middlename'][0]
						  . "</option>\n";
    }
    
    $competitions = get_posts( array( 'post_type' => 'ocm_comp'
                            , 'numberposts' => '-1'
                            , 'order'=> 'ASC'
                            , 'orderby' => 'title'
                            ) );

    $comp_options = "<option value=''> ".__('none', TEXT_DOMAIN)." </option>\n";
    foreach ($competitions as $comp) {
		$comp_values = get_post_custom($comp->ID);
		$groupid = (($comp->ID != "") ? $comp_values['_groupid'][0] : "");
        $comp_options .= "<option " . (($competitionid == $comp->ID) ? "selected='selected' " : "") . "value='$comp->ID'>" 
                        . get_the_title($comp->ID) . (($groupid != "") ? (" - [" . get_the_title( $groupid ) . "]") : "")
						. "</option>\n";
    }
    
    $html = "
        <tr> 
        <td align='right'><label>".__('Member', TEXT_DOMAIN)."</label></td>
        <td><input type='radio' name='entry_type' value='member' ". $member_checked ."/>
        <select name='member'>\n". $member_options ."</select></td>
        </tr>
        <tr>
        <td align='right'><label>".__('Name', TEXT_DOMAIN)."</label></td>
        <td><input type='radio' name='entry_type' value='external' ". $external_checked ."/>
        <input id='name' type='text' name='name' value='$name' /></td>
        </tr>
        <tr>
        <td align='right'><label>".__('Team Name', TEXT_DOMAIN)."</label></td>
        <td><input type='radio' name='entry_type' value='team' ". $team_checked ."/>
        <input id='name' type='text' name='teamname' value='$teamname' /></td>
        </tr>
        <tr>
        <td align='right'><label>".__('Disqualified', TEXT_DOMAIN)."</label></td><td>
        <input type='checkbox' name='disqualified' value='yes' ". $disqualified_checked ." /></td>
        " . apply_filters( 'ocm_entry_display_format_fields', "", $post->ID, $competitionid ) . "
        <tr>
        <td align='right'><label>".__('Competition', TEXT_DOMAIN)."</label></td>
        <td><select name='competitionid'>\n". $comp_options ."</select></td>
        </tr>
        ";
    echo "<table>" . $html . "</table>";

}
function ocm_entry_bulk() {
	
	$description = array( 
        __('Define entries', TEXT_DOMAIN),
		__('View and select entries for creation', TEXT_DOMAIN),
		__('Confirm', TEXT_DOMAIN),
		__('Creating entries', TEXT_DOMAIN),
	);
						
	$html_step1 = "
		<p>
		<table>
		<tr>
		<td><label>".__('Member Club', TEXT_DOMAIN)."</label></td>
		<td>".
        wp_dropdown_categories(
            array(
                'show_option_none' => __('select club from list...', TEXT_DOMAIN),
                'taxonomy' => 'ocm_member_category',
                'id' => 'bulk_member_category',
                'orderby' => 'name',
                'show_count' => true,
                'hide_empty' => false,
                'hierarchical' => 1, 
                'depth' => 4,
                'echo' => 0
			)
        ) ."
		</td>
		</tr>
		<tr>
		<td><label>".__('Display format', TEXT_DOMAIN)."</label></td>
		<td><input type='radio' name='entry_type' id='formatF' value='f' selected='selected'/>" . "First name only" . "</td>
		<td>(".__('example', TEXT_DOMAIN).": John)</td>
		</tr>
		<tr>
		<td></td>
		<td><input type='radio' name='entry_type' id='formatFMN' value='fmn'/>". "Full name, starting with first name" ."</td>
		<td>(".__('example', TEXT_DOMAIN).": John F Kennedy)</td>
		</tr>
		<tr>
		<td></td>
		<td><input type='radio' name='entry_type' id='formatNFM' value='nfm'/>". "Full name" ."</td>
		<td>(".__('example', TEXT_DOMAIN).": Kennedy, John F)</td>
		</tr>
		</table>
		</p>";
	
	echo ocm_bulk_import_wizard($description, $html_step1);
}
function ocm_entry_display_team() {
    global $post;
 
    $players = get_posts( array( 'post_type' => 'ocm_teamplayer'
                                , 'numberposts' => '-1'
                                , 'meta_key' => '_entryid'
                                , 'meta_value' => $post->ID
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
function ocm_entry_list_shortcodes() {
	global $post;
	
	$html = __('Use the following shortcodes by copy/pasting into a post or page (see documentation).', TEXT_DOMAIN);

	$html .= sprintf("<p>[ocm_team_data entry_id='%s']</p>", $post->ID);
	$html .= sprintf("<p>[ocm_match_data entry_id='%s']</p>", $post->ID);
	
	echo $html;
}

function ocm_entry_save_fields($post_id) {
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

        // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['ocm_entry_frm_nonce']) || !wp_verify_nonce($_POST['ocm_entry_frm_nonce'], 'ocm_entry_frm_nonce'))
        return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_posts'))
        return;

    if (isset($_POST['entry_type'])) {
        $type = $_POST['entry_type'];
        update_post_meta($post_id, '_entry_type', $type);
        switch ( $type ) {
            case 'member' : {
                update_post_meta($post_id, '_name', '');
                if (isset($_POST['member']))
                    update_post_meta($post_id, '_member', esc_attr($_POST['member']));
                break;
            }
            case 'external' : {
                update_post_meta($post_id, '_member', '');
                if (isset($_POST['name']))
                    update_post_meta($post_id, '_name', esc_attr($_POST['name']));
                break;
            }
            case 'team' : {
                update_post_meta($post_id, '_member', '');
                if (isset($_POST['name']))
                    update_post_meta($post_id, '_name', esc_attr($_POST['teamname']));
                break;
            }
        }
    }

    if (isset($_POST['disqualified']))
        update_post_meta($post_id, '_disqualified', esc_attr($_POST['disqualified']));
	
	$competition_id = -1;
    if (isset($_POST['competitionid'])) {
		$competition_id = esc_attr($_POST['competitionid']);
        update_post_meta($post_id, '_competitionid', $competition_id);
	}

	do_action( 'ocm_entry_save_format_fields', $post_id, $competition_id );     
}

// display comp data on admin page

function ocm_entry_columns($defaults) {  
    // no author, (post)date
    unset($defaults['author']);  
    unset($defaults['date']);  
    
    // new column titles
    $defaults['name']  = __('Name', TEXT_DOMAIN);
    $defaults['member']  = __('Member', TEXT_DOMAIN);
    $defaults['disqualified']  = __('Disqualified', TEXT_DOMAIN);
    $defaults['competition']  = __('Competition', TEXT_DOMAIN);
    
    return $defaults;  
}  
  
function ocm_entry_sortable_column( $columns ) {  
    $columns['name'] = 'name';  
    $columns['competition'] = 'competition';  
    
    //To make a column 'un-sortable' remove it from the array  
    //unset($columns['date']);  
  
    return $columns;  
}   
  
function ocm_entry_column_content($column_name, $post_ID) {  
    switch($column_name){
        case 'competition':
            $id = get_post_meta( $post_ID , '_competitionid' , true );
            echo sprintf("%s", get_the_title( $id )
                );
            return;
        case 'member':
            $member = get_post_meta( $post_ID , '_member' , true );
            echo sprintf("%s", $member
                );
            return;
        case 'name':
            $name = get_post_meta( $post_ID , '_name' , true );
            echo sprintf("%s", $name
                );
            return;
        case 'disqualified':
            $disqualified = get_post_meta( $post_ID , '_disqualified' , true );
            echo sprintf("%s", $disqualified
                );
            return;
        default:
            echo print_r($post_ID,true); //Show the whole array for troubleshooting purposes
            return;
    }
} 

// rest api
function ocm_entry_register_rest_fields(){
	register_rest_field( 'ocm_entry', 'name', array( /*'get_callback' => 'ocm_member_get_name' 
														, */'update_callback' => 'ocm_entry_update' 
														, 'schema' => array( 'description' => __('Name', TEXT_DOMAIN) , 'type' => 'string', 'required' => 'false' )) ); 
	register_rest_field( 'ocm_entry', 'member_id', array( /*'get_callback' => 'ocm_member_get_middle_name' 
															 , */'update_callback' => 'ocm_entry_update' 
															 , 'schema' => array( 'description' => 'ocm_member post id' , 'type' => 'integer', 'required' => 'false' )) ); 
	register_rest_field( 'ocm_entry', 'entry_type', array( /*'get_callback' => 'ocm_member_get_first_name' 
																   , */'update_callback' => 'ocm_entry_update' 
																   , 'schema' => array( 'description' => __('entry type', TEXT_DOMAIN) , 'type' => 'string', 'required' => 'true' )) ); 
	register_rest_field( 'ocm_entry', 'competition_id', array( 'get_callback' => 'ocm_entry_get_competition_id' 
																  , 'update_callback' => 'ocm_entry_update' 
																  , 'schema' => array( 'description' => 'ocm_comp post id' , 'type' => 'integer', 'required' => 'true' )) ); 
}
//function ocm_member_get_name($post) { return get_post_meta( $post['id'], '_name', true ); }
//function ocm_member_get_middle_name($post) { return get_post_meta( $post['id'], '_middlename', true ); }
//function ocm_member_get_first_name($post) { return get_post_meta( $post['id'], '_firstname', true ); }
function ocm_entry_get_competition_id($post) { return get_post_meta( $post['id'], '_competitionid', true ); }

function ocm_entry_update($value, $post, $key) { 
	switch ( $key ) {
		case 'name' 			:  update_post_meta( $post->ID, '_name', $value ); break;
		case 'member_id' 		:  update_post_meta( $post->ID, '_member', $value ); break;
		case 'entry_type' 	:  update_post_meta( $post->ID, '_entry_type', $value ); break;
		case 'competition_id' 	:  update_post_meta( $post->ID, '_competitionid', $value ); break;
		default : return false;
	}
	return true; 
}
