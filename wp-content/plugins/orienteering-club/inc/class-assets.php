<?php
/**
 * Enqueue scripts and styles for the front end.
 *
 * @since custom 1.2
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'OSM_PluginAssets' ) ) {

    Class OSM_PluginAssets
    {

        public function __construct()
        {
            /**
             * Enqueue plugin assets
             */
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'ocm_assets' ), 99 );

            /**
             * Enqueue Admin panel additional styles and scripts
             */
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'ocm_admin_assets' ) );

            /**
             * Optimize JS and CSS files
             */
            add_filter( 'script_loader_tag', array( __CLASS__, 'deferJS_parser' ), 11, 1 );
        }

        /**
         * Enqueue scripts and styles for the front end.
         */
        public static function ocm_assets()
        {
            wp_enqueue_script( 'jquery' );

            wp_enqueue_style( 'ocm_spclmgt', plugins_url( '../css/spclmgt.css', __FILE__ ) );
            wp_enqueue_script( 'ocm_spclmgt', plugins_url( '../js/index.js', __FILE__ ), array( 'jquery'), NULL, true );
            wp_localize_script( 'ocm_spclmgt', 'ocm',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'success_message' => __("You are successfuly been added", TEXT_DOMAIN),
                )
            );
        }

        /**
         * Load Admin Panel styles and js
         */
        public static function ocm_admin_assets()
        {
            global $pagenow, $typenow;
            
            if (  (($pagenow == 'post-new.php') || ($pagenow == 'post.php')) 
            //&& (($typenow == 'ocm_member') || ($typenow == 'ocm_invoice') || ($typenow == 'ocm_match')) 
            ) {  
                wp_enqueue_style( 'ocm_admin_spclmgt', plugins_url( '/css/spclmgt-admin.css', __FILE__ ) );
                wp_enqueue_style( 'jquery-style', plugins_url( '/css/jquery-ui.css', __FILE__ ) );
                wp_enqueue_script( 'ocm_spclmgt-admin', plugins_url( '/js/spclmgt.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-datepicker'), '20151231', true ); 
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

        /**
         * Defer parsing for js files - https://www.w3schools.com/tags/att_script_defer.asp
         *
         * @param string $url,
         * @return string $url
         */
        public static function deferJS_parser( $url )
        {
            if ( is_user_logged_in() ) return $url;

            // do not edit if not a js files
            if (FALSE === strpos($url, '.js')) return $url;
            if (strpos($url, 'jquery.js')) return $url;

            return str_replace( ' src', ' defer src', $url );
        }

    }

    new OSM_PluginAssets();
}