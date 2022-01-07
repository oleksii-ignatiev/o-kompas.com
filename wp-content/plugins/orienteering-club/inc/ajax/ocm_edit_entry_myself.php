<?php

if( !function_exists('ocm_edit_entry_myself') ){

    add_action( 'wp_ajax_ocm_edit_entry_myself', 'ocm_edit_entry_myself' );
    add_action( 'wp_ajax_nopriv_ocm_edit_entry_myself', 'ocm_edit_entry_myself' );

    function ocm_edit_entry_myself(){
        global $post;

        $errors = array();
        $has_errors = false;
        $success = false;
        $success_message = '';
       
		$user_id = (int) $_POST['userID'];
		$event_id = (int) $_POST['eventID'];
		$group = (int) $_POST['group'];
        
        $entries = get_post_meta($event_id, 'entry', true) ?: array();
        $current_user_id = wp_get_current_user()->ID;
        
        // Let's do some error handling
        if ( !isset($user_id)){
            $has_errors = true;
            $errors[] = __('ID is undefined', TEXT_DOMAIN);
        }

        if ( $user_id !== $current_user_id){
            $has_errors = true;
            $errors[] = __('You can not delete this entry', TEXT_DOMAIN);
        }
        
        if ( !in_array($user_id, array_column($entries, 'id')) ) {
            $has_errors = true;
            $errors[] = __('You haven\'t been entered yet this event', TEXT_DOMAIN);
        }
        
        if ( !$group ) {
            $has_errors = true;
            $errors[] = __('Category is not defined', TEXT_DOMAIN);
        }

        // Let's trigger the call
        if ( !$has_errors ) {
            $entry = array();
            $icons = '';

            if ( user_can($current_user_id,'edit_posts') ) $icons .= get_edit_button($event_id, $event_columns[$group]);
            if ( user_can($current_user_id,'delete_posts') ) $icons .= get_delete_button($event_id);
            $entry['icons'] = $icons;

            $entry['id'] = $user_id;
            foreach ($event_columns as $column) {
                $entry[$column] = $column == 'Группа' ? $group : get_user_entry_data($column);
            }
            
            $entries[]=$entry;
            update_post_meta($event_id, 'entry', $entries);
            $success = true;    
        }

        $return = array(
            'entries' => $entries,
		    'entry' => $entry,
            'has_errors' => $has_errors,
		    'errors' => $errors,
            'success' => $success
		);

	    wp_send_json($return);
    }

    
}