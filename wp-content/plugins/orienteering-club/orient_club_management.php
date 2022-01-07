<?php
/*
 * Plugin Name: Orienteering Club Management
  * Description: Create members, competitions etc. for your club. Easy to manage and to publish on your site. 
 * Version: 1.0.0
 * Author: Oleksii Ignatiev
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: o-kompas
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setting constants
define('OCM_VERSION', "1.0.0");        // version
define('TEXT_DOMAIN', "o-kompas");       
define('OCM_DIR', dirname( __FILE__ )); // absolute path to plugin directory
define('INC', OCM_DIR .'/inc/');       
define('IMAGES', OCM_DIR .'/images/');       
define('TEMPLATES', '/templates/');  


include_once INC . 'bulk_import.php';
include_once INC . 'qualifications.php';

include_once INC . 'user.php';
include_once INC . 'roles.php';

/**
 * Classes with customizations for Custom Post Types and Custom Taxonomies
 */
foreach ( glob( INC . 'post_types/*.php' ) as $filename ) {
    include_once $filename;
}

/**
 * Include plugin helpers plugin filters.
 * Additional functions for plugin to update standard WP functionality
 */
foreach ( glob( INC . 'functions/*.php' ) as $filename ) {
    include_once $filename;
}

/**
 * Separated ajax functions
 * Each file for single ajax action
 */
foreach ( glob( INC . 'ajax/*.php' ) as $filename ) {
    include_once $filename;
}

/**
 * Include site JS and CSS (frontend and admin panel side)
 */
include_once INC . 'class-assets.php';

// include_once INC . 'members.php';
// include_once INC . 'members_shortcodes.php';
// include_once INC . 'members_widgets.php';
// if ('use_invoices' == get_option('ocm_include_invoices_data')) {
//     include_once INC . 'invoices.php';
//     include_once INC . 'invoices_shortcodes.php';
// }
// if ('use_competitions' == get_option('ocm_include_competitions_data')) {
//     include_once INC . 'competitions.php';
//     include_once INC . 'compgroups.php';
//     include_once INC . 'matches.php';
//     include_once INC . 'competitors.php';
//     include_once INC . 'teamplayers.php';
//     include_once INC . 'competitions_shortcodes.php';
//     include_once INC . 'compgroups_shortcodes.php';
//     include_once INC . 'matches_shortcodes.php';
//     include_once INC . 'competitors_shortcodes.php';
//     include_once INC . 'matches_widgets.php';
// }

register_activation_hook( __FILE__, 'ocm_plugin_activate');

register_deactivation_hook( __FILE__, 'ocm_plugin_deactivate');


add_filter('plugins_loaded','ocm_plugins_loaded');
function ocm_plugins_loaded() {
    load_plugin_textdomain( TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

// add_action( 'wp_enqueue_scripts', 'ocm_enqueue_scripts' );
// function ocm_enqueue_scripts() {
// 	wp_enqueue_style( 'ocm_spclmgt', plugins_url( '/css/spclmgt.css', __FILE__ ) );
//     wp_enqueue_script( 'ocm_spclmgt', plugins_url( '/js/index.js', __FILE__ ), array( 'jquery'), NULL, true );
//     wp_localize_script( 'ocm_spclmgt', 'ocm',
//         array(
//             // 'asset_directory' => get_stylesheet_directory_uri().'/assets/_dist/images/',
//             'ajax_url' => admin_url( 'admin-ajax.php' ),
//             // 'phone_get_a_quote' => get_field('phone_get_a_quote', 'options'),
//             // 'phone_file_a_claim' => get_field('phone_file_a_claim', 'options'),
//             'success_message' => __("You are successfuly been added", TEXT_DOMAIN),
//         )
//     );
// }

// add_action( 'admin_enqueue_scripts', 'ocm_admin_enqueue_scripts' );
// function ocm_admin_enqueue_scripts() {
//     global $pagenow, $typenow;
    
//     if (  (($pagenow == 'post-new.php') || ($pagenow == 'post.php')) 
//        //&& (($typenow == 'ocm_member') || ($typenow == 'ocm_invoice') || ($typenow == 'ocm_match')) 
//        ) {
//         wp_enqueue_style( 'ocm_admin_spclmgt', plugins_url( '/css/spclmgt-admin.css', __FILE__ ) );
//         wp_enqueue_style( 'jquery-style', plugins_url( '/css/jquery-ui.css', __FILE__ ) );
//         wp_enqueue_script( 'ocm_spclmgt-admin', plugins_url( '/js/spclmgt.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-datepicker'), '20151231', true ); 
//     }
	
// 	/* global bulk js */
//     if (  ($pagenow == 'post-new.php') 
//        && (($typenow == 'ocm_member') || ($typenow == 'ocm_invoice') || ($typenow == 'ocm_match') || ($typenow == 'ocm_competitor') || ($typenow == 'ocm_teamplayer')) 
//        ) {
// 		wp_enqueue_script( 'wp-api' );
// 		wp_enqueue_script( 'ocm_bulk_js', plugins_url( '/js/ocm_bulk.js', __FILE__ ), array( 'wp-api' ), '20171231', true ); 
// 		$global_data = array(
// 			'map_strings' => ocm_bulk_strings(),
// 		);
// 		wp_localize_script( 'ocm_bulk_js', 'ocm_bulk_globals', $global_data );
//     }
// 	/* invoice bulk js */
//     if (($pagenow == 'post-new.php') && ($typenow == 'ocm_invoice')) {
// 		wp_enqueue_script( 'ocm_invoice_bulk_js', plugins_url( '/js/ocm_invoice_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
// 		$global_data = array(
// 			'nonce' => wp_create_nonce( 'wp_rest' ),
// 			'url_done' => admin_url( '/edit.php?post_type=ocm_invoice', __FILE__ ),
// 		);
// 		wp_localize_script( 'ocm_invoice_bulk_js', 'ocm_invoice_bulk_globals', $global_data );
//     }
// 	/* member bulk js */
//     if (($pagenow == 'post-new.php') && ($typenow == 'ocm_member')) {
// 		wp_enqueue_script( 'ocm_member_bulk_js', plugins_url( '/js/ocm_member_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
// 		$global_data = array(
// 			'nonce' => wp_create_nonce( 'wp_rest' ),
// 			'url_done' => admin_url( '/edit.php?post_type=ocm_member', __FILE__ ),
// 			'mapheader' => ocm_member_import(),
// 		);
// 		wp_localize_script( 'ocm_member_bulk_js', 'ocm_member_bulk_globals', $global_data );
//     }
// 	/* match bulk js */
//     if (($pagenow == 'post-new.php') && ($typenow == 'ocm_match')) {
// 		wp_enqueue_script( 'ocm_match_bulk_js', plugins_url( '/js/ocm_match_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
// 		$global_data = array(
// 			'nonce' => wp_create_nonce( 'wp_rest' ),
// 			'url_done' => admin_url( '/edit.php?post_type=ocm_match', __FILE__ ),
// 		);
// 		wp_localize_script( 'ocm_match_bulk_js', 'ocm_match_bulk_globals', $global_data );
//     }
// 	/* competitor bulk js */
//     if (($pagenow == 'post-new.php') && ($typenow == 'ocm_competitor')) {
// 		wp_enqueue_script( 'ocm_competitor_bulk_js', plugins_url( '/js/ocm_competitor_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
// 		$global_data = array(
// 			'nonce' => wp_create_nonce( 'wp_rest' ),
// 			'url_done' => admin_url( '/edit.php?post_type=ocm_competitor', __FILE__ ),
// 		);
// 		wp_localize_script( 'ocm_competitor_bulk_js', 'ocm_competitor_bulk_globals', $global_data );
//     }
// 	/* team player bulk js */
//     if (($pagenow == 'post-new.php') && ($typenow == 'ocm_teamplayer')) {
// 		wp_enqueue_script( 'ocm_teamplayer_bulk_js', plugins_url( '/js/ocm_teamplayer_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
// 		$global_data = array(
// 			'nonce' => wp_create_nonce( 'wp_rest' ),
// 			'url_done' => admin_url( '/edit.php?post_type=ocm_teamplayer', __FILE__ ),
// 		);
// 		wp_localize_script( 'ocm_teamplayer_bulk_js', 'ocm_teamplayer_bulk_globals', $global_data );
//     }
// }

// register custom post types
// add_action('init','ocm_custom_post_init');
function ocm_custom_post_init() {

    ocm_member_custom_post_init();    
    if ('use_invoices' == get_option('ocm_include_invoices_data')) {
        ocm_invoice_custom_post_init();
    }
    if ('use_competitions' == get_option('ocm_include_competitions_data')) {
        ocm_comp_custom_post_init();    
        ocm_comp_group_custom_post_init();    
        ocm_match_custom_post_init();    
        ocm_competitor_custom_post_init();    
        ocm_teamplayer_custom_post_init();
    }
}

// options and settings in admin menu
add_action('admin_menu' , 'ocm_admin_settings_page'); 
function ocm_admin_settings_page() {
    add_menu_page (
        __('Orienteering Club', TEXT_DOMAIN),
        __('Orienteering Club', TEXT_DOMAIN),
        'edit_others_posts',
        'orientclub',
        '',
        'dashicons-groups',
        '45.13'
    );
    // add_submenu_page('orientclub',
    //     __('Orienteering Club Management', TEXT_DOMAIN).' - '.__('All Clubs', TEXT_DOMAIN), 
    //     __('All Clubs', TEXT_DOMAIN), 
    //     'edit_others_posts', 
    //     'ocm_all_categories_page', 
    //     'ocm_create_all_categories_page'
    // );
    // add_submenu_page('orientclub',
    //     __('Orienteering Club Management', TEXT_DOMAIN).' - '.__('Export Data', TEXT_DOMAIN), 
    //     __('Export Data', TEXT_DOMAIN), 
    //     'edit_others_posts', 
    //     'ocm_export_data_page', 
    //     'ocm_create_admin_export_page'
    // );
    // add_submenu_page('orientclub',
    //     __('Orienteering Club Management', TEXT_DOMAIN).' - '.__('General Options', TEXT_DOMAIN), 
    //     __('General Options', TEXT_DOMAIN), 
    //     'activate_plugins', 
    //     'ocm_general_options_page', 
    //     'ocm_create_admin_general_page'
    // );
}

function ocm_create_all_categories_page() {
    //must check that the user has the required capability 
    if (!current_user_can( 'edit_others_posts' )) {
        wp_die( __('You do not have sufficient permissions to access this page.', TEXT_DOMAIN) );
    }
?>    
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2><?php _e('Orienteering Club Management', TEXT_DOMAIN) ?> - <?php _e('All Clubs', TEXT_DOMAIN) ?></h2>
</div>
<?php
    
    include(INC . 'members_categories_page.php');
    if ('use_invoices' == get_option('ocm_include_invoices_data')) {
        include(INC . 'invoices_categories_page.php');
    }
    if ('use_competitions' == get_option('ocm_include_competitions_data')) {
        include(INC . 'competitions_categories_page.php');
    }
}

function ocm_create_admin_export_page() {
    //must check that the user has the required capability 
    if (!current_user_can( 'edit_others_posts' )) {
        wp_die( __('You do not have sufficient permissions to access this page.', TEXT_DOMAIN) );
    }
    
    include(INC . 'members_export.php');
    if ('use_invoices' == get_option('ocm_include_invoices_data')) {
        include(INC . 'invoices_export.php');
    }
    if ('use_competitions' == get_option('ocm_include_competitions_data')) {
        include(INC . 'competitions_export.php');
    }
}

function ocm_create_admin_general_page() {
    //must check that the user has the required capability 
    if (!current_user_can( 'activate_plugins' )) {
        wp_die( __('You do not have sufficient permissions to access this page.', TEXT_DOMAIN) );
    }
?>    
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2><span class="dashicons dashicons-admin-generic"></span> <?php _e('Club Management', TEXT_DOMAIN) ?> - <?php _e('General Options', TEXT_DOMAIN) ?></h2>
    <p> <?php _e('OCM plugin version', TEXT_DOMAIN) ?> ( <?php echo get_option('ocm_version'); ?> ) </p>
</div>
<?php
    
    // include(INC . 'members_options_settings.php');
    // include(INC . 'invoices_options_settings.php');
    // include(INC . 'competitions_options_settings.php');
}


