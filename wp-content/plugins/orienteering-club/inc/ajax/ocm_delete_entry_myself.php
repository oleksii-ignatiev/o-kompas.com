<?php

if( !function_exists('ocm_delete_entry_myself') ) {

    add_action( 'wp_ajax_ocm_delete_entry_myself', 'ocm_delete_entry_myself' );
    add_action( 'wp_ajax_nopriv_ocm_delete_entry_myself', 'ocm_delete_entry_myself' );

    function ocm_delete_entry_myself() {
        global $post;

        $errors = array();
        $has_errors = false;
        $success = false;
        $success_message = '';
       
		$user_id = (int) $_POST['userID'];
		$event_id = (int) $_POST['eventID'];

        if ( !isset($user_id)){
            $has_errors = true;
            $errors[] = __('ID is undefined', TEXT_DOMAIN);
        }
        
        $entries = get_post_meta($event_id, 'entry', true) ?: array();
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;

        if ( $user_id !== $current_user_id){
            $has_errors = true;
            $errors[] = __('You can not delete this entry', TEXT_DOMAIN);
        }
        
        if ( !in_array($user_id, array_column($entries, 'id')) ) {
            $has_errors = true;
            $errors[] = __('You haven\'t been entered yet this event', TEXT_DOMAIN);
        }
        
        // Let's trigger the call
        if ( !$has_errors ) {
            
            $key = array_search($user_id, $entries);
            array_splice($entries, $key-1, 1);
            update_post_meta($event_id, 'entry', $entries);
            
            $success = true;
            $success_message = __('You have been successfuly removed from this event', TEXT_DOMAIN);
        }

        $return = array(
            
            '$entries' => $entries,
            'has_errors' => $has_errors,
		    'errors' => $errors,
            'success' => $success,
            'success_message' => $success_message,
		);

	    wp_send_json($return);
    }
}