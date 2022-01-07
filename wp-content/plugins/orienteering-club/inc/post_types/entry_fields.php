<?php

if ( ! defined( 'OCM_DIR' ) ) {
	exit;
}

if ( ! class_exists( 'OSM_Post_Types_Entry_Fields' ) ) {

	class OSM_Post_Types_Entry_Fields {

		function __construct(){
			add_action( 'init', array( __CLASS__, 'register_post_types' ) );
			
		}

		public static function register_post_types() {
			if ( !is_blog_installed() ) return;

			if ( !post_type_exists( 'entry_fields' ) ) {
				register_post_type(
					'entry_fields',
					array(
						'labels'       => array(
							'name'                  => __( 'Entry Fields', TEXT_DOMAIN ),
							'singular_name'         => _x( 'Entry Field', 'Site post type singular name', TEXT_DOMAIN ),
							'add_new'               => __( 'Add Entry Field', TEXT_DOMAIN ),
							'add_new_item'          => __( 'Add New Entry Field', TEXT_DOMAIN ),
							'edit'                  => __( 'Edit', TEXT_DOMAIN ),
							'edit_item'             => __( 'Edit Entry Field', TEXT_DOMAIN ),
							'new_item'              => __( 'New Entry Field', TEXT_DOMAIN ),
							'view'                  => __( 'View Entry Fields', TEXT_DOMAIN ),
							'view_item'             => __( 'View Entry Field', TEXT_DOMAIN ),
							'search_items'          => __( 'Search Entry Fields', TEXT_DOMAIN ),
							'not_found'             => __( 'No Entry Fields found', TEXT_DOMAIN ),
							'not_found_in_trash'    => __( 'No Entry Fields found in trash', TEXT_DOMAIN ),
							'parent'                => __( 'Parent Entry Field', TEXT_DOMAIN ),
							'menu_name'             => _x( 'Entry Fields', 'Admin menu name', TEXT_DOMAIN ),
							'filter_items_list'     => __( 'Filter Entry Fields', TEXT_DOMAIN ),
							'items_list_navigation' => __( 'Entry Field navigation', TEXT_DOMAIN ),
							'items_list'            => __( 'Entry Field list', TEXT_DOMAIN ),
						),
						
						'menu_icon'             => 'dashicons-universal-access',
						'supports'              => array( 'title'),
                        'rewrite' => array('slug' => 'entry_field'),
						'capability_type'     => 'post',
						'show_ui' => true,
						'show_in_rest' => true,
						'has_archive' => false,

                        'public' => false,
                        'show_ui' => true,
                        'show_in_menu' => 'orientclub',
                        'show_in_admin_bar' => true,
                        'menu_position' => 150,
                        'show_in_nav_menus' => true,
                        'publicly_queryable' => false,
                        'exclude_from_search' => true,
                        'query_var' => true,
                        'can_export' => true,
                        'map_meta_cap' => true,
					)
				);
			}
		}
	
	}

	new OSM_Post_Types_Entry_Fields();
}