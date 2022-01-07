<?php
/**
 * Create roles and capabilities
 */

global $wp_roles;

if ( class_exists( 'WP_Roles' ) ) :
    if ( ! isset( $wp_roles ) ) :
        $wp_roles = new WP_Roles();
    endif;
endif;

if ( is_object( $wp_roles ) ) :

    add_role(
        'sp_player',
        __( 'Player', TEXT_DOMAIN ),
        array(
            'level_1'                   => true,
            'level_0'                   => true,

            'read'                      => true,
            'delete_posts'              => true,
            'edit_posts'                => true,
            'upload_files'              => true,

            'read_private_entries'      => true,
            'delete_private_entries'    => true,
            'edit_private_entries'      => true,
            
            'edit_sp_player'            => true,
            'read_sp_player'            => true,
            'edit_sp_players'           => true,
            'edit_published_sp_players' => true,
            'assign_sp_player_terms'    => true,

            'edit_sp_event'             => true,
            'read_sp_event'             => true,
            'edit_sp_events'            => true,
            'edit_published_sp_events'  => true,
            'assign_sp_event_terms'     => true,

            'edit_sp_team'              => true,
            'read_sp_team'              => true,
            'edit_sp_teams'             => true,
            'edit_published_sp_teams'   => true,
            'assign_sp_team_terms'      => true,
        )
    );

    add_role(
        'sp_staff',
        __( 'Staff', TEXT_DOMAIN ),
        array(
            'level_1'                   => true,
            'level_0'                   => true,

            'read'                      => true,
            'delete_posts'              => true,
            'edit_posts'                => true,
            'upload_files'              => true,

            'edit_sp_staff'             => true,
            'read_sp_staff'             => true,
            'edit_sp_staffs'            => true,
            'edit_published_sp_staffs'  => true,
            'assign_sp_staff_terms'     => true,

            'edit_sp_event'             => true,
            'read_sp_event'             => true,
            'edit_sp_events'            => true,
            'edit_published_sp_events'  => true,
            'assign_sp_event_terms'     => true,

            'edit_sp_team'              => true,
            'read_sp_team'              => true,
            'edit_sp_teams'             => true,
            'edit_published_sp_teams'   => true,
            'assign_sp_team_terms'      => true,

            'edit_sp_player'            => true,
            'read_sp_player'            => true,
            'edit_sp_players'           => true,
            'edit_published_sp_players' => true,
            'assign_sp_player_terms'    => true,
        )
    );

    add_role(
        'sp_event_manager',
        __( 'Event Manager', TEXT_DOMAIN ),
        array(
            'level_1'                    => true,
            'level_0'                    => true,

            'read'                       => true,
            'delete_posts'               => true,
            'edit_posts'                 => true,
            'upload_files'               => true,
            'manage_categories'          => true,

            'edit_sp_event'              => true,
            'read_sp_event'              => true,
            'delete_sp_event'            => true,
            'edit_sp_events'             => true,
            'edit_others_sp_events'      => true,
            'publish_sp_events'          => true,
            'delete_sp_events'           => true,
            'delete_published_sp_events' => true,
            'edit_published_sp_events'   => true,
            'manage_sp_event_terms'      => true,
            'edit_sp_event_terms'        => true,
            'delete_sp_event_terms'      => true,
            'assign_sp_event_terms'      => true,

            'edit_sp_team'               => true,
            'read_sp_team'               => true,
            'edit_sp_teams'              => true,
            'edit_published_sp_teams'    => true,
            'assign_sp_team_terms'       => true,

            'edit_sp_player'             => true,
            'read_sp_player'             => true,
            'edit_sp_players'            => true,
            'edit_published_sp_players'  => true,
            'assign_sp_player_terms'     => true,

            'edit_sp_staff'              => true,
            'read_sp_staff'              => true,
            'edit_sp_staffs'             => true,
            'edit_published_sp_staffs'   => true,
            'assign_sp_staff_terms'      => true,
        )
    );

    add_role(
        'sp_team_manager',
        __( 'Team Manager', TEXT_DOMAIN ),
        array(
            'level_2'                     => true,
            'level_1'                     => true,
            'level_0'                     => true,

            'read'                        => true,
            'delete_posts'                => true,
            'edit_posts'                  => true,
            'delete_published_posts'      => true,
            'publish_posts'               => true,
            'upload_files'                => true,
            'edit_published_posts'        => true,

            'edit_sp_player'              => true,
            'read_sp_player'              => true,
            'delete_sp_player'            => true,
            'edit_sp_players'             => true,
            'edit_others_sp_players'      => true,
            'publish_sp_players'          => true,
            'delete_sp_players'           => true,
            'delete_published_sp_players' => true,
            'edit_published_sp_players'   => true,
            'assign_sp_player_terms'      => true,

            'edit_sp_staff'               => true,
            'read_sp_staff'               => true,
            'delete_sp_staff'             => true,
            'edit_sp_staffs'              => true,
            'edit_others_sp_staffs'       => true,
            'publish_sp_staffs'           => true,
            'delete_sp_staffs'            => true,
            'delete_published_sp_staffs'  => true,
            'edit_published_sp_staffs'    => true,
            'assign_sp_staff_terms'       => true,

            'edit_sp_event'               => true,
            'read_sp_event'               => true,
            'delete_sp_event'             => true,
            'edit_sp_events'              => true,
            'edit_others_sp_events'       => true,
            'publish_sp_events'           => true,
            'delete_sp_events'            => true,
            'delete_published_sp_events'  => true,
            'edit_published_sp_events'    => true,
            'manage_sp_event_terms'       => true,
            'edit_sp_event_terms'         => true,
            'delete_sp_event_terms'       => true,
            'assign_sp_event_terms'       => true,

            'edit_sp_team'                => true,
            'read_sp_team'                => true,
            'edit_sp_teams'               => true,
            'edit_published_sp_teams'     => true,
            'assign_sp_team_terms'        => true,

            'edit_sp_list'                => true,
            'read_sp_list'                => true,
            'delete_sp_list'              => true,
            'edit_sp_lists'               => true,
            'publish_sp_lists'            => true,
            'delete_sp_lists'             => true,
            'delete_published_sp_lists'   => true,
            'edit_published_sp_lists'     => true,
            'assign_sp_list_terms'        => true,
        )
    );

    add_role(
        'sp_league_manager',
        __( 'League Manager', TEXT_DOMAIN ),
        array(
            'level_7'                => true,
            'level_6'                => true,
            'level_5'                => true,
            'level_4'                => true,
            'level_3'                => true,
            'level_2'                => true,
            'level_1'                => true,
            'level_0'                => true,

            'read'                   => true,
            'read_private_pages'     => true,
            'read_private_posts'     => true,
            'edit_users'             => true,
            'edit_posts'             => true,
            'edit_pages'             => true,
            'edit_published_posts'   => true,
            'edit_published_pages'   => true,
            'edit_private_pages'     => true,
            'edit_private_posts'     => true,
            'edit_others_posts'      => true,
            'edit_others_pages'      => true,
            'publish_posts'          => true,
            'publish_pages'          => true,
            'delete_posts'           => true,
            'delete_pages'           => true,
            'delete_private_pages'   => true,
            'delete_private_posts'   => true,
            'delete_published_pages' => true,
            'delete_published_posts' => true,
            'delete_others_posts'    => true,
            'delete_others_pages'    => true,
            'manage_categories'      => true,
            'manage_links'           => true,
            'moderate_comments'      => true,
            'unfiltered_html'        => true,
            'upload_files'           => true,
            'export'                 => true,
            'import'                 => true,
            'list_users'             => true,
        )
    );

    // $capabilities = $this->get_core_capabilities();

    // foreach ( $capabilities as $cap_group ) :
    //     foreach ( $cap_group as $cap ) :
    //         $wp_roles->add_cap( 'sp_league_manager', $cap );
    //         $wp_roles->add_cap( 'administrator', $cap );
    //     endforeach;
    // endforeach;
endif;