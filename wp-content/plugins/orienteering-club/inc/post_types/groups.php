<?php

if ( ! defined( 'OCM_DIR' ) ) {
	exit;
}

if ( ! class_exists( 'OSM_Post_Types_Groups' ) ) {

	class OSM_Post_Types_Groups {

		function __construct(){
			add_action( 'init', array( __CLASS__, 'register_post_types' ) );

			// if ( class_exists('acf') && is_admin() ) {
            //     add_filter( 'manage_jobs_posts_columns', array(__CLASS__, 'job_admin_columns') );
            //     add_action( 'manage_jobs_posts_custom_column' , array(__CLASS__, 'job_admin_columns_data'), 10, 2 );
			// 	add_filter( 'manage_edit-jobs_sortable_columns', array(__CLASS__, 'job_sortable_column') );
			// 	add_action( 'pre_get_posts', array(__CLASS__, 'job_orderby') );
			// 	add_action( 'restrict_manage_posts', array(__CLASS__, 'job_input') );
			// 	add_action( 'pre_get_posts', array(__CLASS__, 'job_filter')); 
            // }
		}

		public static function register_post_types() {
			if ( !is_blog_installed() ) return;

			if ( !post_type_exists( 'groups' ) ) {
				register_post_type(
					'groups',
					array(
						'labels'       => array(
							'name'                  => __( 'Groups', TEXT_DOMAIN ),
							'singular_name'         => _x( 'Group', 'Site post type singular name', TEXT_DOMAIN ),
							'add_new'               => __( 'Add Group', TEXT_DOMAIN ),
							'add_new_item'          => __( 'Add New Group', TEXT_DOMAIN ),
							'edit'                  => __( 'Edit', TEXT_DOMAIN ),
							'edit_item'             => __( 'Edit Group', TEXT_DOMAIN ),
							'new_item'              => __( 'New Group', TEXT_DOMAIN ),
							'view'                  => __( 'View Groups', TEXT_DOMAIN ),
							'view_item'             => __( 'View Group', TEXT_DOMAIN ),
							'search_items'          => __( 'Search Groups', TEXT_DOMAIN ),
							'not_found'             => __( 'No Groups found', TEXT_DOMAIN ),
							'not_found_in_trash'    => __( 'No Groups found in trash', TEXT_DOMAIN ),
							'parent'                => __( 'Parent Group', TEXT_DOMAIN ),
							'menu_name'             => _x( 'Groups', 'Admin menu name', TEXT_DOMAIN ),
							'filter_items_list'     => __( 'Filter Groups', TEXT_DOMAIN ),
							'items_list_navigation' => __( 'Group navigation', TEXT_DOMAIN ),
							'items_list'            => __( 'Group list', TEXT_DOMAIN ),
						),
						// 'menu_position'         => 6,
						'menu_icon'             => 'dashicons-universal-access',
						'supports'              => array( 'title'),
                        'rewrite' => array('slug' => 'group'),
						'capability_type'     => 'post',
						'show_ui' => true,
						'show_in_rest' => true,
						'has_archive' => false,

                        'public' => false,
                        'show_ui' => true,
                        'show_in_menu' => 'orientclub',
                        'show_in_admin_bar' => true,
                        'menu_position' => 140,
                        'show_in_nav_menus' => true,
                        'publicly_queryable' => false,
                        'exclude_from_search' => true,
                        // 'taxonomies' => array( 'ocm_member_category' ),
                        'query_var' => true,
                        'can_export' => true,
                        // 'rewrite' => array( 'slug' => 'member'),
                        // 'capability_type' => 'page', 
                        'map_meta_cap' => true,
					)
				);
			}
		}

		// public static function job_admin_columns($columns) {
		// 	$original_date_col = $columns['date'];
		// 	unset( $columns['date'] );
			
        //     $columns['job_id']     = __( 'Job ID', TEXT_DOMAIN );
		// 	$columns['department'] = __( 'Department', TEXT_DOMAIN );
		// 	$columns['location']   = __( 'Location', TEXT_DOMAIN );
		// 	$columns['date']       = $original_date_col;

        //     return $columns;
        // }

        // public static function job_admin_columns_data( $column, $post_id ) {
        //     switch ( $column ) {
        //         case 'job_id' :
        //             $job_id = get_post_meta( $post_id, 'job_id', true );
        //             echo $job_id;
        //             break;

		// 		case 'department' :
		// 			$department = get_post_meta( $post_id, 'department', true );
		// 			echo $department;
		// 			break;

		// 		case 'location' :
		// 			$location = get_post_meta( $post_id, 'location', true );
		// 			echo $location;
		// 			break;
        //     }
        // }
		
		// public static function job_sortable_column( $columns ) {
		// 	$columns['job_id'] = 'job_id';
		// 	$columns['department'] = 'department';
		// 	$columns['location'] = 'location';
		
		// 	return $columns;
		// }

		
		// public static function job_orderby( $query ) {
		// 	if( ! is_admin() )
		// 		return;
		
		// 	$orderby = $query->get( 'orderby');
			
		// 	if( 'job_id' == $orderby ) {
		// 		$query->set('meta_key','job_id');
		// 		$query->set('orderby','meta_value_num');
		// 	}

		// 	if( 'department' == $orderby ) {
		// 		$query->set('meta_key','department');
		// 		$query->set('orderby','meta_value');
		// 	}

		// 	if( 'location' == $orderby ) {
		// 		$query->set('meta_key','location');
		// 		$query->set('orderby','meta_value');
		// 	}
		// }

		// public static function job_input() {
		// 	$scr = get_current_screen();
		// 	if ( $scr->base !== 'edit' || $scr->post_type !== 'jobs') return;

		// 	$selected = filter_input(INPUT_GET, 'job_id', FILTER_SANITIZE_STRING );

		// 	echo "<input name='job_id' type='text' class='current-page' value='{$selected}' placeholder='Job ID'>";
		// }

		// public static function job_filter($query) {
		// 	if ( is_admin() && $query->is_main_query() ) {
		// 		$scr = get_current_screen();
		// 		if ( $scr->base !== 'edit' && $scr->post_type !== 'jobs' ) return;

		// 		if (isset($_GET['job_id']) && $_GET['job_id'] !== '') {
		// 			$query->set('meta_query', array( array(
		// 			'key' => 'job_id',
		// 			'value' => sanitize_text_field($_GET['job_id'])
		// 			) ) );
		// 		}
		// 	}
		// }
	}

	new OSM_Post_Types_Groups();
}

// function ocm_member_custom_post_init(){
    
//     $labels = array(
//         'name' => __('Clubs', TEXT_DOMAIN),
//         'singular_name' => __('Club', TEXT_DOMAIN),
//         'search_items' => __('Search Clubs', TEXT_DOMAIN),
//         'all_items' => __('All Clubs', TEXT_DOMAIN),
//         'parent_item' => __('Parent Club', TEXT_DOMAIN),
//         'parent_item_colon' => __('Parent Club:', TEXT_DOMAIN),
//         'edit_item' => __('Edit Club', TEXT_DOMAIN), 
//         'update_item' => __('Update Club', TEXT_DOMAIN),
//         'add_new_item' => __('Add New Club', TEXT_DOMAIN),
//         'new_item_name' => __('New Club', TEXT_DOMAIN),
//         'menu_name' => __('Clubs', TEXT_DOMAIN),
//     ); 	
//     register_taxonomy('ocm_member_category',array('ocm_member'), array(
//         'hierarchical' => false,
//         'labels' => $labels,
//         'show_ui' => true,
//         'query_var' => true,
//         'rewrite' => array( 'slug' => 'ocm_member_category' ),
//         'show_in_rest' => true,
//         'show_in_menus' => true,
//         'show_in_quick_edit' => true,
        
//         'rest_base' => 'member_categories',
//         // 'meta_box_cb' => 'post_categories_meta_box',
//     ));

//     // custom post type: Member
//     $labels = array(
//         'name' => __('Members', TEXT_DOMAIN),
//         'singular_name' => __('Member', TEXT_DOMAIN),
//         'add_new' => __('Add New Member', TEXT_DOMAIN),
//         'add_new_item' => __('Add New Member', TEXT_DOMAIN),
//         'edit_item' => __('Edit Member', TEXT_DOMAIN),
//         'new_item' => __('New Member', TEXT_DOMAIN),
//         'view_item' => __('View Member', TEXT_DOMAIN),
//         'search_items' => __('Search Members', TEXT_DOMAIN),
//         'not_found' => __('No members found', TEXT_DOMAIN),
//         'not_found_in_trash' => __('No members found in Trash', TEXT_DOMAIN),
//         'parent_item_colon' => __('Parent Member:', TEXT_DOMAIN),
//         'menu_name' => __('Members', TEXT_DOMAIN),
//     );
//     $args = array(
//         'labels' => $labels,
//         'hierarchical' => false,
//         'supports' => array('title','author','editor','thumbnail'),
//         'public' => true,
//         'show_ui' => true,
//         'show_in_menu' => 'orientclub',
//         'show_in_admin_bar' => true,
//         'menu_position' => 110,
//         'show_in_nav_menus' => true,
//         'publicly_queryable' => true,
//         'exclude_from_search' => false,
//         'taxonomies' => array( 'ocm_member_category' ),
//         'query_var' => true,
//         'can_export' => true,
//         'rewrite' => array( 'slug' => 'member'),
//         'capability_type' => 'page', 
//         'map_meta_cap' => true,
//         'show_in_rest' => true,
//         'rest_base' => 'members',
//     );
//     register_post_type('ocm_member', $args);
// }
