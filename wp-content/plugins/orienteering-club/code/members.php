<?php

function ocm_member_custom_post_init(){
    // taxonomy: Member Club
    $labels = array(
        'name' => __('Member Clubs', 'o-kompas'),
        'singular_name' => __('Member Club', 'o-kompas'),
        'search_items' => __('Search Member Clubs', 'o-kompas'),
        'all_items' => __('All Member Clubs', 'o-kompas'),
        'parent_item' => __('Parent Member Club', 'o-kompas'),
        'parent_item_colon' => __('Parent Member Club:', 'o-kompas'),
        'edit_item' => __('Edit Member Club', 'o-kompas'), 
        'update_item' => __('Update Member Club', 'o-kompas'),
        'add_new_item' => __('Add New Member Club', 'o-kompas'),
        'new_item_name' => __('New Member Club', 'o-kompas'),
        'menu_name' => __('Member Clubs', 'o-kompas'),
    ); 	
    register_taxonomy('ocm_member_category',array('ocm_member'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'ocm_member_category' ),
        'show_in_rest' => true,
        'rest_base' => 'member_categories',
    ));

    // custom post type: Member
    $labels = array(
        'name' => __('Members', 'o-kompas'),
        'singular_name' => __('Member', 'o-kompas'),
        'add_new' => __('Add New Member', 'o-kompas'),
        'add_new_item' => __('Add New Member', 'o-kompas'),
        'edit_item' => __('Edit Member', 'o-kompas'),
        'new_item' => __('New Member', 'o-kompas'),
        'view_item' => __('View Member', 'o-kompas'),
        'search_items' => __('Search Members', 'o-kompas'),
        'not_found' => __('No members found', 'o-kompas'),
        'not_found_in_trash' => __('No members found in Trash', 'o-kompas'),
        'parent_item_colon' => __('Parent Member:', 'o-kompas'),
        'menu_name' => __('Members', 'o-kompas'),
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
        'taxonomies' => array( 'ocm_member_category' ),
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
        'capability_type' => 'page', 
        'map_meta_cap' => true,
        'show_in_rest' => true,
        'rest_base' => 'members',
    );
    register_post_type('ocm_member', $args);
}

// hooks and filters for Member
add_action('add_meta_boxes', 'ocm_member_add_fields_box');
add_action('save_post', 'ocm_member_save_fields');
add_filter('manage_ocm_member_posts_columns', 'ocm_member_columns');  
add_filter('manage_edit-ocm_member_sortable_columns', 'ocm_member_sortable_column'); 
add_filter('request', 'ocm_member_name_orderby' );
add_action('manage_ocm_member_posts_custom_column', 'ocm_member_column_content', 10, 2);   
add_action('restrict_manage_posts', 'ocm_member_filtered_by_category');
add_filter('parse_query', 'ocm_member_category_from_id_in_query');
add_action('rest_api_init', 'ocm_member_register_rest_fields');

// member fields operations

function ocm_member_add_fields_box() {
    global $pagenow, $typenow;

    add_meta_box('ocm_member_fields_box_id', __('Member Data', 'o-kompas'), 'ocm_member_display_fields', 'ocm_member');
	if ($pagenow == 'post-new.php') {
		add_meta_box('ocm_member_bulk_box_id', __('Import Members from a CSV file', 'o-kompas'), 'ocm_member_bulk', 'ocm_member');
	}
    if ($pagenow == 'post.php') {
		do_action('ocm_invoice_list_member_invoices');
        add_meta_box('ocm_member_shortcodes_box_id', __('Shortcodes', 'o-kompas'), 'ocm_member_list_shortcodes', 'ocm_member', 'side', 'high');
    }
}
function ocm_member_display_fields() {
    global $post;
 
    $values = get_post_custom($post->ID);
    
    $firstname = isset($values['_firstname']) ? esc_attr($values['_firstname'][0]) : '';
    $middlename = isset($values['_middlename']) ? esc_attr($values['_middlename'][0]) : '';
    $name = isset($values['_name']) ? esc_attr($values['_name'][0]) : '';

    $street = isset($values['_street']) ? esc_attr($values['_street'][0]) : '';
    $number = isset($values['_number']) ? esc_attr($values['_number'][0]) : '';
    $zip = isset($values['_zip']) ? esc_attr($values['_zip'][0]) : '';
    $place = isset($values['_place']) ? esc_attr($values['_place'][0]) : '';
 
    $phone = isset($values['_phone']) ? esc_attr($values['_phone'][0]) : '';
    $cell = isset($values['_cell']) ? esc_attr($values['_cell'][0]) : '';

    $email = isset($values['_email']) ? esc_attr($values['_email'][0]) : '';

    $dateofbirth = isset($values['_dateofbirth']) ? esc_attr($values['_dateofbirth'][0]) : '0000-00-00';
    $gender = isset($values['_gender']) ? esc_attr($values['_gender'][0]) : 'male';
    if ($gender == 'male') { 
        $male_checked = 'checked'; 
        $female_checked = ''; 
    } else { 
        $male_checked = ''; 
        $female_checked = 'checked'; 
    }
    
    $level = isset($values['_level']) ? esc_attr($values['_level'][0]) : '';
    
    $custom1 = isset($values['_custom1']) ? esc_attr($values['_custom1'][0]) : '';
    $custom2 = isset($values['_custom2']) ? esc_attr($values['_custom2'][0]) : '';
    $custom3 = isset($values['_custom3']) ? esc_attr($values['_custom3'][0]) : '';
    $custom4 = isset($values['_custom4']) ? esc_attr($values['_custom4'][0]) : '';
    
    $label_custom1 = get_option('ocm_custom1_member'); 
    if ($label_custom1 == '') { $label_custom1 = '[Custom1]'; }
    $label_custom2 = get_option('ocm_custom2_member'); 
    if ($label_custom2 == '') { $label_custom2 = '[Custom2]'; }
    $label_custom3 = get_option('ocm_custom3_member'); 
    if ($label_custom3 == '') { $label_custom3 = '[Custom3]'; }
    $label_custom4 = get_option('ocm_custom4_member'); 
    if ($label_custom4 == '') { $label_custom4 = '[Custom4]'; }

    $startdate = isset($values['_startdate']) ? esc_attr($values['_startdate'][0]) : '0000-00-00';
    $enddate = isset($values['_enddate']) ? esc_attr($values['_enddate'][0]) : '0000-00-00';
    $username = isset($values['_username']) ? esc_attr($values['_username'][0]) : '';

    wp_nonce_field('ocm_member_frm_nonce', 'ocm_member_frm_nonce');
 
    $html = "
        <tr><td align='right'><label>".__('Name', 'o-kompas')."</label></td><td><input id='name' type='text' name='name' value='$name' /></td></tr>
        <tr><td align='right'><label>".__('Middle', 'o-kompas')."</label></td><td><input id='middlename' type='text' name='middlename' value='$middlename' /></td></tr>
        <tr><td align='right'><label>".__('First name', 'o-kompas')."</label></td><td><input id='firstname' type='text' name='firstname' value='$firstname' /></td></tr>
        <tr><td align='right'><label>".__('Street', 'o-kompas')."</label></td><td><input id='street' type='text' name='street' value='$street' /></td></tr>
        <tr><td align='right'><label>".__('Number', 'o-kompas')."</label></td><td><input id='number' type='text' name='number' value='$number' /></td></tr>
        <tr><td align='right'><label>".__('Zip', 'o-kompas')."</label></td><td><input id='zip' type='text' name='zip' value='$zip' /></td></tr>
        <tr><td align='right'><label>".__('City', 'o-kompas')."</label></td><td><input id='place' type='text' name='place' value='$place' /></td></tr>
        <tr><td align='right'><label>".__('Phone', 'o-kompas')."</label></td><td><input id='phone' type='text' name='phone' value='$phone' /></td></tr>
        <tr><td align='right'><label>".__('Cell', 'o-kompas')."</label></td><td><input id='cell' type='text' name='cell' value='$cell' /></td></tr>
        <tr><td align='right'><label>".__('E-mail', 'o-kompas')."</label></td><td><input id='email' type='text' name='email' value='$email' /></td></tr>
        <tr><td align='right'><label>".__('Date of birth', 'o-kompas')."</label></td><td><input id='dateofbirth' class='scm_datepicker' type='text' name='dateofbirth' value='$dateofbirth' /></td></tr>
        <tr><td align='right'><label>".__('Gender', 'o-kompas')."</label></td><td>
            <input type='radio' name='gender' value='male' {$male_checked}/> ".__('Male', 'o-kompas')."
            <input type='radio' name='gender' value='female' {$female_checked}/> ".__('Female', 'o-kompas')."</td>
        </tr>
        <tr><td align='right'><label>".__('Level', 'o-kompas')."</label></td><td><input id='level' type='text' name='level' value='$level' /></td></tr>
        <tr><td align='right'><label>{$label_custom1}</label></td><td><input id='custom1' type='text' name='custom1' value='{$custom1}' /></td></tr>
        <tr><td align='right'><label>{$label_custom2}</label></td><td><input id='custom2' type='text' name='custom2' value='{$custom2}' /></td></tr>
        <tr><td align='right'><label>{$label_custom3}</label></td><td><input id='custom3' type='text' name='custom3' value='{$custom3}' /></td></tr>
        <tr><td align='right'><label>{$label_custom4}</label></td><td><input id='custom4' type='text' name='custom4' value='{$custom4}' /></td></tr>
        <tr><td align='right'><label>".__('Start date membership', 'o-kompas')."</label></td><td><input id='startdate' class='scm_datepicker' type='text' name='startdate' value='$startdate' /></td></tr>
        <tr><td align='right'><label>".__('End date membership', 'o-kompas')."</label></td><td><input id='enddate' class='scm_datepicker' type='text' name='enddate' value='$enddate' /></td></tr>
        <tr><td align='right'><label>".__('User name', 'o-kompas')."</label></td><td><input id='username' type='text' name='username' value='$username' /></td></tr>
        ";
    echo "<table>" . $html . "</table>";
}
function ocm_member_bulk() {
	
	$description = array( 
        __('Specify CSV-file', 'o-kompas'),
		__('View and select entries for creation', 'o-kompas'),
		__('Confirm', 'o-kompas'),
		__('Creating entries', 'o-kompas'),
    );
						
	$label_custom1 = get_option('ocm_custom1_member') ?: 'custom1';
	$label_custom2 = get_option('ocm_custom2_member') ?: 'custom2';
	$label_custom3 = get_option('ocm_custom3_member') ?: 'custom3';
	$label_custom4 = get_option('ocm_custom4_member') ?: 'custom4';
	$custom_labels = $label_custom1.", ".$label_custom2.", ".$label_custom3.", ".$label_custom4;
					
	$html_step1 = "
		<p>
		<table>
		<tr>
		<td><label>".__('CSV file', 'o-kompas')."</label></td>
		<td><input id='filename' type='file'></td>
		<td>
			<p>
				<ol>
					<li>
						".__('CSV file must have field names in first row', 'o-kompas')."
					</li>
					<li> 
						".__('Field names must include', 'o-kompas').":<br>
						".__('first name', 'o-kompas').",
						".__('name', 'o-kompas')." 
					</li>
					<li> 
						".__('Additional field names to be used', 'o-kompas').":<br>
						".__('middle name', 'o-kompas').", 
						".__('street', 'o-kompas').",
						".__('number', 'o-kompas').",
						".__('city', 'o-kompas').",
						".__('zip', 'o-kompas').",
						".__('e-mail', 'o-kompas').",
						".__('phone', 'o-kompas').",
						".__('cell', 'o-kompas').",
						".__('date of birth', 'o-kompas').",
						".__('gender', 'o-kompas').",
						".__('level', 'o-kompas').",
						".__('start date', 'o-kompas').",
						".__('end date', 'o-kompas').",
						$custom_labels,
						".__('user name', 'o-kompas')."
					</li>
					<li> 
						".__('Fields must be seperated by a comma', 'o-kompas')."
					</li>
				</ol>
			</p>
		</td>
		</tr>
		<tr><td><a href='".plugins_url( '/templates/members.csv', dirname(__FILE__) )."'>".__('example file', 'o-kompas')."</a><br>(".__('english', 'o-kompas').")</td></tr>
		</table>
		</p>";
	
	echo ocm_bulk_import_wizard($description, $html_step1);
}
function ocm_member_list_shortcodes() {
	global $post;
	
	$html = __('Use the following shortcodes by copy/pasting into a post or page (see documentation).', 'o-kompas');

	$html .= sprintf("<p>[ocm_member_data member_id='%s']</p>", $post->ID);
	$html .= sprintf("<p>[ocm_members]</p>", $post->ID);
	$html .= sprintf("<p>[ocm_member_username_link]</p>", $post->ID);
	if ('use_invoices' == get_option('ocm_include_invoices_data')) {
		$html .= sprintf("<p>[ocm_invoice_data member_id='%s']</p>", $post->ID);
	}
	if ('use_competitions' == get_option('ocm_include_competitions_data')) {
		$html .= sprintf("<p>[ocm_competition_data member_id='%s']</p>", $post->ID);
	}
	
	echo $html;
}

function ocm_member_save_fields($post_id) {
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
 
    // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['ocm_member_frm_nonce']) || !wp_verify_nonce($_POST['ocm_member_frm_nonce'], 'ocm_member_frm_nonce'))
        return;
 
    // if our current user can't edit this post, bail
    if (!current_user_can('edit_posts'))
        return;
        
    if (isset($_POST['name']))
        update_post_meta($post_id, '_name', esc_attr($_POST['name']));
    if (isset($_POST['middlename']))
        update_post_meta($post_id, '_middlename', esc_attr($_POST['middlename']));
    if (isset($_POST['firstname']))
        update_post_meta($post_id, '_firstname', esc_attr($_POST['firstname']));
    if (isset($_POST['street']))
        update_post_meta($post_id, '_street', esc_attr($_POST['street']));
    if (isset($_POST['number']))
        update_post_meta($post_id, '_number', esc_attr($_POST['number']));
    if (isset($_POST['zip']))
        update_post_meta($post_id, '_zip', esc_attr($_POST['zip']));
    if (isset($_POST['place']))
        update_post_meta($post_id, '_place', esc_attr($_POST['place']));
    if (isset($_POST['phone']))
        update_post_meta($post_id, '_phone', esc_attr($_POST['phone']));
    if (isset($_POST['cell']))
        update_post_meta($post_id, '_cell', esc_attr($_POST['cell']));
    if (isset($_POST['email']))
        update_post_meta($post_id, '_email', esc_attr($_POST['email']));
    if (isset($_POST['dateofbirth']))
        update_post_meta($post_id, '_dateofbirth', esc_attr($_POST['dateofbirth']));
    if (isset($_POST['gender']))
        update_post_meta($post_id, '_gender', esc_attr($_POST['gender']));
    if (isset($_POST['level']))
        update_post_meta($post_id, '_level', esc_attr($_POST['level']));
    if (isset($_POST['custom1']))
        update_post_meta($post_id, '_custom1', esc_attr($_POST['custom1']));
    if (isset($_POST['custom2']))
        update_post_meta($post_id, '_custom2', esc_attr($_POST['custom2']));
    if (isset($_POST['custom3']))
        update_post_meta($post_id, '_custom3', esc_attr($_POST['custom3']));
    if (isset($_POST['custom4']))
        update_post_meta($post_id, '_custom4', esc_attr($_POST['custom4']));
    if (isset($_POST['startdate']))
        update_post_meta($post_id, '_startdate', esc_attr($_POST['startdate']));
    if (isset($_POST['enddate']))
        update_post_meta($post_id, '_enddate', esc_attr($_POST['enddate']));
    if (isset($_POST['username']))
        update_post_meta($post_id, '_username', esc_attr($_POST['username']));
        
    // remove action to prevent infinite loop as create_matches() creates/saves posts
    remove_action('save_post', 'ocm_member_save_fields');
    
    do_action( 'ocm_member_create', $post_id );
    
    // reassign action again
    add_action('save_post', 'ocm_member_save_fields');
}

// display member data on admin page

function ocm_member_columns($defaults) {  
    // no author, date
    unset($defaults['author']);  
    unset($defaults['date']);  
    
    // new column titles
    $defaults['name']  = __('Name', 'o-kompas');
    $defaults['telmail']  = __('Phone', 'o-kompas').' / '.__('Mail', 'o-kompas');
    $defaults['category']  = __('Club', 'o-kompas');
    
    $label_custom1 = get_option('ocm_custom1_member') ?: '[Custom1]';
    $label_custom2 = get_option('ocm_custom2_member') ?: '[Custom2]';
    $label_custom3 = get_option('ocm_custom3_member') ?: '[Custom3]';
    $label_custom4 = get_option('ocm_custom4_member') ?: '[Custom4]';
    $defaults['custom']  = $label_custom1 . ' / ' . $label_custom2 . ' / ' . $label_custom3 . ' / ' . $label_custom4;
    
    return $defaults;  
}  

function ocm_member_sortable_column( $columns ) {  
    $columns['name'] = 'name';  
  
    //To make a column 'un-sortable' remove it from the array  
    //unset($columns['date']);  
  
    return $columns;  
}   

function ocm_member_name_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'name' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => '_name',
            'orderby' => 'meta_value'
        ) );
    }
 
    return $vars;
}
  
function ocm_member_column_content($column_name, $post_ID) {  
    switch($column_name){
        case 'name':
            echo sprintf('%s %s, %s', 
                    get_post_meta( $post_ID , '_name' , true ), 
                    get_post_meta( $post_ID , '_middlename' , true ), 
                    get_post_meta( $post_ID , '_firstname' , true )
                );
            return;
        case 'telmail':
            echo sprintf("%s &nbsp &nbsp %s <br> %s", 
                    get_post_meta( $post_ID , '_phone' , true ), 
                    get_post_meta( $post_ID , '_cell' , true ), 
                    get_post_meta( $post_ID , '_email' , true )
                );
            return;
        case 'category':
            $categories = wp_get_post_terms( $post_ID, 'ocm_member_category', array("fields" => "names") );
            echo sprintf("%s",  implode(", ", $categories) );
            return;
        case 'custom':
            echo sprintf("%s <br> %s <br> %s <br> %s", 
                    get_post_meta( $post_ID , '_custom1' , true ),
                    get_post_meta( $post_ID , '_custom2' , true ),
                    get_post_meta( $post_ID , '_custom3' , true ),
                    get_post_meta( $post_ID , '_custom4' , true ) 
                );
            return;
        default:
            echo print_r($post_ID,true); //Show the whole array for troubleshooting purposes
            return;
    }
} 

// filter members by category

function ocm_member_filtered_by_category() {
    global $typenow;
    $post_type = 'ocm_member'; 
    $taxonomy = 'ocm_member_category'; 
    if ($typenow == $post_type) {
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' => __("Show All {$info_taxonomy->label}"),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'show_count' => true,
            'hide_empty' => false,
            'hierarchical' => 1, 
            'depth' => 4,
	        )
        );
    };
}

function ocm_member_category_from_id_in_query($query) {
    global $pagenow;
    $post_type = 'ocm_member'; 
    $taxonomy = 'ocm_member_category'; 
    $q_vars = &$query->query_vars;
    
    if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
    
        // by default, order members by name
        if ( !isset($q_vars['orderby']) ) {
            $q_vars['order'] = 'ASC';
            $q_vars['orderby'] = 'meta_value';
            $q_vars['meta_key'] = '_name';
        }

        // if not filtering on member category, exclude (optional) categories from member list
        if ( !isset($q_vars[$taxonomy]) ) {
            $q_vars['tax_query'] = array(
                                        array(
                                            'taxonomy' => $taxonomy,
                                            'field' => 'id',
                                            'terms' => array( apply_filters('ocm_member_exclude_categories', "") ),      
                                            'operator' => 'NOT IN'
                                        )
                                   );
        } 
        else if ( is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }
}

// rest api
function ocm_member_register_rest_fields(){
	register_rest_field( 'ocm_member', 'name', array( 'get_callback' => 'ocm_member_get_name' 
													, 'update_callback' => 'ocm_member_update_memberid' 
													, 'schema' => array( 'description' => __('Name', 'o-kompas') , 'type' => 'string', 'required' => 'true' )) ); 
	register_rest_field( 'ocm_member', 'middle_name', array( 'get_callback' => 'ocm_member_get_middle_name' 
														   , 'update_callback' => 'ocm_member_update_memberid' 
														   , 'schema' => array( 'description' => __('Middle', 'o-kompas') , 'type' => 'string', 'required' => 'true' )) ); 
	register_rest_field( 'ocm_member', 'first_name', array( 'get_callback' => 'ocm_member_get_first_name' 
														  , 'update_callback' => 'ocm_member_update_memberid' 
														  , 'schema' => array( 'description' => __('First name', 'o-kompas') , 'type' => 'string', 'required' => 'true' )) ); 
	register_rest_field( 'ocm_member', 'street', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Street', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'number', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Number', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'zip', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Zip', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'city', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('City', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'e-mail', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('E-mail', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'phone', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Phone', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'cell', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Cell', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'date_of_birth', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Date of birth', 'o-kompas') , 'type' => 'date')) ); 
	register_rest_field( 'ocm_member', 'gender', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Gender', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'level', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Level', 'o-kompas') , 'type' => 'number')) ); 
	register_rest_field( 'ocm_member', 'start_date', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('Start date', 'o-kompas') , 'type' => 'date')) ); 
	register_rest_field( 'ocm_member', 'end_date', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('End date', 'o-kompas') , 'type' => 'date')) ); 
	register_rest_field( 'ocm_member', 'custom1', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('custom1', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'custom2', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('custom2', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'custom3', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('custom3', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'custom4', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('custom4', 'o-kompas') , 'type' => 'string')) ); 
	register_rest_field( 'ocm_member', 'user_name', array( 'update_callback' => 'ocm_member_update_memberid' 
													  , 'schema' => array( 'description' => __('User name', 'o-kompas') , 'type' => 'string')) ); 
}
function ocm_member_get_name($post) { return get_post_meta( $post['id'], '_name', true ); }
function ocm_member_get_middle_name($post) { return get_post_meta( $post['id'], '_middlename', true ); }
function ocm_member_get_first_name($post) { return get_post_meta( $post['id'], '_firstname', true ); }

function ocm_member_update_memberid($value, $post, $key) { 
	switch ( $key ) {
		case 'name' 		:  update_post_meta( $post->ID, '_name', $value ); break;
		case 'middle_name' 	:  update_post_meta( $post->ID, '_middlename', $value ); break;
		case 'first_name' 	:  update_post_meta( $post->ID, '_firstname', $value ); break;
		case 'street' 		:  update_post_meta( $post->ID, '_street', $value ); break;
		case 'number' 		:  update_post_meta( $post->ID, '_number', $value ); break;
		case 'zip' 			:  update_post_meta( $post->ID, '_zip', $value ); break;
		case 'city' 		:  update_post_meta( $post->ID, '_place', $value ); break;
		case 'e-mail' 		:  update_post_meta( $post->ID, '_email', $value ); break;
		case 'phone' 		:  update_post_meta( $post->ID, '_phone', $value ); break;
		case 'cell' 		:  update_post_meta( $post->ID, '_cell', $value ); break;
		case 'date_of_birth':  update_post_meta( $post->ID, '_dateofbirth', $value ); break;
		case 'gender' 		:  update_post_meta( $post->ID, '_gender', $value ); break;
		case 'level' 		:  update_post_meta( $post->ID, '_level', $value ); break;
		case 'start_date' 	:  update_post_meta( $post->ID, '_startdate', $value ); break;
		case 'end_date' 	:  update_post_meta( $post->ID, '_enddate', $value ); break;
		case 'custom1' 		:  update_post_meta( $post->ID, '_custom1', $value ); break;
		case 'custom2' 		:  update_post_meta( $post->ID, '_custom2', $value ); break;
		case 'custom3' 		:  update_post_meta( $post->ID, '_custom3', $value ); break;
		case 'custom4' 		:  update_post_meta( $post->ID, '_custom4', $value ); break;
		case 'user name' 	:  update_post_meta( $post->ID, '_username', $value ); break;
		default : return false;
	}
	return true; 
}




// generate list of members
function ocm_member_list( $columns, $category ) {
    echo "sep=,\n";
    ocm_member_list_header( $columns );
    echo "\n";

    $members_args = array( 
        'post_type' => 'ocm_member',
        'numberposts' => '-1',
        'meta_key' => '_name',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );    

    if ( $category !== -1 ) {
									
		$members_args['tax_query'] = array(
                                         array(
                                             'taxonomy' => 'ocm_member_category',
                                             'field' => 'id',
                                             'terms' => array( $category ),
                                         )
                                     );
    }
    
    $members = get_posts($members_args);

    foreach ( $members as $member ) {
        ocm_member_list_entry($member->ID, $columns);
        echo "\n";
    }
}

function ocm_member_list_header($columns) {

    $names = array( 
        __("Name", 'o-kompas'), __("First name", 'o-kompas'), __("Middle", 'o-kompas'), 
        __("Street", 'o-kompas'), __("Number", 'o-kompas'), __("Zip", 'o-kompas'), __("City", 'o-kompas'), 
        __("E-mail", 'o-kompas'), 
        __("Categories", 'o-kompas'), 
        __("Phone", 'o-kompas'), __("Cell", 'o-kompas')
    );

    echo '"' . join( array_merge($names, $columns), '","' ) . '"';
}

function ocm_member_list_entry($member_id, $columns) {
    $values = get_post_custom($member_id);

    $firstname = isset($values['_firstname']) ? esc_attr($values['_firstname'][0]) : '';
    $middlename = isset($values['_middlename']) ? esc_attr($values['_middlename'][0]) : '';
    $name = isset($values['_name']) ? esc_attr($values['_name'][0]) : '';

    $street = isset($values['_street']) ? esc_attr($values['_street'][0]) : '';
    $number = isset($values['_number']) ? esc_attr($values['_number'][0]) : '';
    $zip = isset($values['_zip']) ? esc_attr($values['_zip'][0]) : '';
    $place = isset($values['_place']) ? esc_attr($values['_place'][0]) : '';
 
    $phone = isset($values['_phone']) ? esc_attr($values['_phone'][0]) : '';
    $cell = isset($values['_cell']) ? esc_attr($values['_cell'][0]) : '';

    $email = isset($values['_email']) ? esc_attr($values['_email'][0]) : '';

    $dateofbirth = isset($values['_dateofbirth']) ? esc_attr($values['_dateofbirth'][0]) : '0000-00-00';
    $age = date_diff(date_create($dateofbirth), date_create(date('Y-m-d')));        
    $gender = isset($values['_gender']) ? esc_attr($values['_gender'][0]) : 'male';

    $level = isset($values['_level']) ? esc_attr($values['_level'][0]) : '';

    $custom1 = isset($values['_custom1']) ? esc_attr($values['_custom1'][0]) : '';
    $custom2 = isset($values['_custom2']) ? esc_attr($values['_custom2'][0]) : '';
    $custom3 = isset($values['_custom3']) ? esc_attr($values['_custom3'][0]) : '';
    $custom4 = isset($values['_custom4']) ? esc_attr($values['_custom4'][0]) : '';

    $startdate = isset($values['_startdate']) ? esc_attr($values['_startdate'][0]) : '0000-00-00';
    $enddate = isset($values['_enddate']) ? esc_attr($values['_enddate'][0]) : '0000-00-00';
    $username = isset($values['_username']) ? esc_attr($values['_username'][0]) : '';
    
    $categories = get_the_terms( $member_id, 'ocm_member_category' );
    if ( $categories && ! is_wp_error( $categories ) ) {
        $cats = array();
        foreach ( $categories as $cat ) {
            $cats[] = $cat->name;
        }                  
        $terms = join(" ", $cats);
    } else {
        $terms = '';
    }        

    $entries = array( 
        $name, $firstname, $middlename,
        $street, $number, $zip, $place,
        $email,
        $terms,
        $phone, $cell
    ); 
	
	$extras  = array();
	if ( in_array( __("Date of birth", 'o-kompas'), $columns ) ) { array_push($extras, $dateofbirth ); }
	if ( in_array( __("Age", 'o-kompas'), $columns ) ) { array_push($extras, $age->format('%y') ); }
	if ( in_array( __("Gender", 'o-kompas'), $columns ) ) { array_push($extras, $gender ); }
	if ( in_array( __("Level", 'o-kompas'), $columns ) ) { array_push($extras, $level ); }
	if ( in_array( __("Start date membership", 'o-kompas'), $columns ) ) { array_push($extras, $startdate ); }
	if ( in_array( __("End date membership", 'o-kompas'), $columns ) ) { array_push($extras, $enddate ); }
	$label = get_option('ocm_custom1_member'); if ($label == '') { $label = "[Custom1]"; } 
	if ( in_array( $label, $columns ) ) { array_push($extras, $custom1 ); }
	$label = get_option('ocm_custom2_member'); if ($label == '') { $label = "[Custom2]"; } 
	if ( in_array( $label, $columns ) ) { array_push($extras, $custom2 ); }
	$label = get_option('ocm_custom3_member'); if ($label == '') { $label = "[Custom3]"; } 
	if ( in_array( $label, $columns ) ) { array_push($extras, $custom3 ); }
	$label = get_option('ocm_custom4_member'); if ($label == '') { $label = "[Custom4]"; } 
	if ( in_array( $label, $columns ) ) { array_push($extras, $custom4 ); }
	if ( in_array( __("User name", 'o-kompas'), $columns ) ) { array_push($extras, $username ); }
					
    echo '"' . join( array_merge($entries, $extras), '","' ) . '"' ;
}

// map header for import from CSV
function ocm_member_import() { 

    $label_custom1 = get_option('ocm_custom1_member') ?: 'custom1';
    $label_custom2 = get_option('ocm_custom2_member') ?: 'custom2';
    $label_custom3 = get_option('ocm_custom3_member') ?: 'custom3';
    $label_custom4 = get_option('ocm_custom4_member') ?: 'custom4'; 
    
    // map field headers
    $map = array(
        __("name", 'o-kompas') => "name",
        __("first name", 'o-kompas') => "first_name",
        __("middle name", 'o-kompas') => "middle_name",
        __("street", 'o-kompas') => "street",
        __("number", 'o-kompas') => "number",
        __("zip", 'o-kompas') => "zip",
        __("city", 'o-kompas') => "city",
        __("e-mail", 'o-kompas') => "e-mail",
        __("phone", 'o-kompas') => "phone",
        __("cell", 'o-kompas') => "cell",
        __("date of birth", 'o-kompas') => "date_of_birth",
        __("gender", 'o-kompas') => "gender",
        __("level", 'o-kompas') => "level",
        __("start date", 'o-kompas') => "start_date",
        __("end date", 'o-kompas') => "end_date",
        $label_custom1 => "custom1",
        $label_custom2 => "custom2",
        $label_custom3 => "custom3",
        $label_custom4 => "custom4",
        __("user name", 'o-kompas') => "username"
    );
    
	return $map;
}