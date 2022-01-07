<?php

if ( ! function_exists( 'acf_load_post_types' ) ) {
    
    function acf_load_post_types($field) {
        $arg = array(
            'post_type' => 'groups',
            'posts_per_page' => -1,
        );
        $query = get_posts($arg);
        
        foreach ( $query as $category ) {
            $field['choices'][$category->ID] = $category->post_title;
        }
        
        return $field;
    }

    add_filter('acf/load_field/name=event_categories', 'acf_load_post_types');
}


/**
 * Rename default post type labels
 *
 * @param string $title,
 * @hooked enter_title_here
 * @return string $title
 */
if ( ! function_exists( 'ocm_new_title_text' ) ) {

    function ocm_new_title_text($title, $post) {

        if ('entry_fields' == $post->post_type) {
            $title = __('Add New Field Title', TEXT_DOMAIN);
        }

        return $title;
    }

    add_filter('enter_title_here', 'ocm_new_title_text', 10, 2);
}

// function ds_disable_feed() {
//     wp_die( __( 'No feed available' ) );
// }

// add_action('do_feed', 'ds_disable_feed', 1);
// add_action('do_feed_rdf', 'ds_disable_feed', 1);
// add_action('do_feed_rss', 'ds_disable_feed', 1);
// add_action('do_feed_rss2', 'ds_disable_feed', 1);
// add_action('do_feed_atom', 'ds_disable_feed', 1);
// add_action('do_feed_rss2_comments', 'ds_disable_feed', 1);
// add_action('do_feed_atom_comments', 'ds_disable_feed', 1);

// remove_action( 'wp_head', 'feed_links_extra', 3 );
// remove_action( 'wp_head', 'feed_links', 2 );