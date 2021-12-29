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
define('OCM_DIR', dirname( __FILE__ )); // absolute path to plugin directory

include_once 'code/bulk_import.php';

include_once 'code/members.php';
include_once 'code/members_shortcodes.php';
include_once 'code/members_widgets.php';
if ('use_invoices' == get_option('ocm_include_invoices_data')) {
    include_once 'code/invoices.php';
    include_once 'code/invoices_shortcodes.php';
}
if ('use_competitions' == get_option('ocm_include_competitions_data')) {
    include_once 'code/competitions.php';
    include_once 'code/compgroups.php';
    include_once 'code/matches.php';
    include_once 'code/competitors.php';
    include_once 'code/teamplayers.php';
    include_once 'code/competitions_shortcodes.php';
    include_once 'code/compgroups_shortcodes.php';
    include_once 'code/matches_shortcodes.php';
    include_once 'code/competitors_shortcodes.php';
    include_once 'code/matches_widgets.php';
}

register_activation_hook( __FILE__,'ocm_plugin_activate');
function ocm_plugin_activate() {
    $old_version = get_option('ocm_version');
	if( $old_version == '' ) {
        // update version
        update_option('ocm_version', OCM_VERSION);
    }
    else if ( OCM_VERSION > $old_version ) {
        // upgrade from earlier plugin version
        
        
        // update version
        update_option('ocm_version', OCM_VERSION);
    }
    
}

register_deactivation_hook( __FILE__,'ocm_plugin_deactivate');
function ocm_plugin_deactivate() {
	//delete_option('ocm_version');
}

add_filter('plugins_loaded','ocm_plugins_loaded');
function ocm_plugins_loaded() {
    load_plugin_textdomain( 'o-kompas', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

add_action( 'wp_enqueue_scripts', 'ocm_enqueue_scripts' );
function ocm_enqueue_scripts() {
	wp_enqueue_style( 'ocm_spclmgt', plugins_url( '/css/spclmgt.css', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'ocm_admin_enqueue_scripts' );
function ocm_admin_enqueue_scripts() {
    global $pagenow, $typenow;
    
    if (  (($pagenow == 'post-new.php') || ($pagenow == 'post.php')) 
       && (($typenow == 'ocm_member') || ($typenow == 'ocm_invoice') || ($typenow == 'ocm_match')) 
       ) {
        wp_enqueue_style( 'jquery-style', plugins_url( '/css/jquery-ui.css', __FILE__ ) );
        wp_enqueue_script( 'ocm_spclmgt', plugins_url( '/js/spclmgt.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-datepicker'), '20151231', true ); 
    }
	
	/* global bulk js */
    if (  ($pagenow == 'post-new.php') 
       && (($typenow == 'ocm_member') || ($typenow == 'ocm_invoice') || ($typenow == 'ocm_match') || ($typenow == 'ocm_competitor') || ($typenow == 'ocm_teamplayer')) 
       ) {
		wp_enqueue_script( 'wp-api' );
		wp_enqueue_script( 'ocm_bulk_js', plugins_url( '/js/ocm_bulk.js', __FILE__ ), array( 'wp-api' ), '20171231', true ); 
		$global_data = array(
			'map_strings' => ocm_bulk_strings(),
		);
		wp_localize_script( 'ocm_bulk_js', 'ocm_bulk_globals', $global_data );
    }
	/* invoice bulk js */
    if (($pagenow == 'post-new.php') && ($typenow == 'ocm_invoice')) {
		wp_enqueue_script( 'ocm_invoice_bulk_js', plugins_url( '/js/ocm_invoice_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
		$global_data = array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'url_done' => admin_url( '/edit.php?post_type=ocm_invoice', __FILE__ ),
		);
		wp_localize_script( 'ocm_invoice_bulk_js', 'ocm_invoice_bulk_globals', $global_data );
    }
	/* member bulk js */
    if (($pagenow == 'post-new.php') && ($typenow == 'ocm_member')) {
		wp_enqueue_script( 'ocm_member_bulk_js', plugins_url( '/js/ocm_member_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
		$global_data = array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'url_done' => admin_url( '/edit.php?post_type=ocm_member', __FILE__ ),
			'mapheader' => ocm_member_import(),
		);
		wp_localize_script( 'ocm_member_bulk_js', 'ocm_member_bulk_globals', $global_data );
    }
	/* match bulk js */
    if (($pagenow == 'post-new.php') && ($typenow == 'ocm_match')) {
		wp_enqueue_script( 'ocm_match_bulk_js', plugins_url( '/js/ocm_match_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
		$global_data = array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'url_done' => admin_url( '/edit.php?post_type=ocm_match', __FILE__ ),
		);
		wp_localize_script( 'ocm_match_bulk_js', 'ocm_match_bulk_globals', $global_data );
    }
	/* competitor bulk js */
    if (($pagenow == 'post-new.php') && ($typenow == 'ocm_competitor')) {
		wp_enqueue_script( 'ocm_competitor_bulk_js', plugins_url( '/js/ocm_competitor_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
		$global_data = array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'url_done' => admin_url( '/edit.php?post_type=ocm_competitor', __FILE__ ),
		);
		wp_localize_script( 'ocm_competitor_bulk_js', 'ocm_competitor_bulk_globals', $global_data );
    }
	/* team player bulk js */
    if (($pagenow == 'post-new.php') && ($typenow == 'ocm_teamplayer')) {
		wp_enqueue_script( 'ocm_teamplayer_bulk_js', plugins_url( '/js/ocm_teamplayer_bulk.js', __FILE__ ), array( 'wp-api', 'ocm_bulk_js' ), '20171231', true ); 
		$global_data = array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'url_done' => admin_url( '/edit.php?post_type=ocm_teamplayer', __FILE__ ),
		);
		wp_localize_script( 'ocm_teamplayer_bulk_js', 'ocm_teamplayer_bulk_globals', $global_data );
    }
}

// register custom post types
add_action('init','ocm_custom_post_init');
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
        __('Orienteering Club', 'o-kompas'),
        __('Orienteering Club', 'o-kompas'),
        'edit_others_posts',
        'orientclub',
        '',
        'dashicons-groups',
        '45.13'
    );
    add_submenu_page('orientclub',
        __('Orienteering Club Management', 'o-kompas').' - '.__('All Clubs', 'o-kompas'), 
        __('All Clubs', 'o-kompas'), 
        'edit_others_posts', 
        'ocm_all_categories_page', 
        'ocm_create_all_categories_page'
    );
    add_submenu_page('orientclub',
        __('Orienteering Club Management', 'o-kompas').' - '.__('Export Data', 'o-kompas'), 
        __('Export Data', 'o-kompas'), 
        'edit_others_posts', 
        'ocm_export_data_page', 
        'ocm_create_admin_export_page'
    );
    add_submenu_page('orientclub',
        __('Orienteering Club Management', 'o-kompas').' - '.__('General Options', 'o-kompas'), 
        __('General Options', 'o-kompas'), 
        'activate_plugins', 
        'ocm_general_options_page', 
        'ocm_create_admin_general_page'
    );
}

function ocm_create_all_categories_page() {
    //must check that the user has the required capability 
    if (!current_user_can( 'edit_others_posts' )) {
        wp_die( __('You do not have sufficient permissions to access this page.', 'o-kompas') );
    }
?>    
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2><?php _e('Orienteering Club Management', 'o-kompas') ?> - <?php _e('All Clubs', 'o-kompas') ?></h2>
</div>
<?php
    
    include('code/members_categories_page.php');
    if ('use_invoices' == get_option('ocm_include_invoices_data')) {
        include('code/invoices_categories_page.php');
    }
    if ('use_competitions' == get_option('ocm_include_competitions_data')) {
        include('code/competitions_categories_page.php');
    }
}

function ocm_create_admin_export_page() {
    //must check that the user has the required capability 
    if (!current_user_can( 'edit_others_posts' )) {
        wp_die( __('You do not have sufficient permissions to access this page.', 'o-kompas') );
    }
    
    include('code/members_export.php');
    if ('use_invoices' == get_option('ocm_include_invoices_data')) {
        include('code/invoices_export.php');
    }
    if ('use_competitions' == get_option('ocm_include_competitions_data')) {
        include('code/competitions_export.php');
    }
}

function ocm_create_admin_general_page() {
    //must check that the user has the required capability 
    if (!current_user_can( 'activate_plugins' )) {
        wp_die( __('You do not have sufficient permissions to access this page.', 'o-kompas') );
    }
?>    
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2><span class="dashicons dashicons-admin-generic"></span> <?php _e('Club Management', 'o-kompas') ?> - <?php _e('General Options', 'o-kompas') ?></h2>
    <p> <?php _e('OCM plugin version', 'o-kompas') ?> ( <?php echo get_option('ocm_version'); ?> ) </p>
</div>
<?php
    
    include('code/members_options_settings.php');
    include('code/invoices_options_settings.php');
    include('code/competitions_options_settings.php');
}


