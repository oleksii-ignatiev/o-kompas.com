<?php

if( !function_exists('ocm_entry_myself') ){

    add_action( 'wp_ajax_ocm_entry_myself', 'ocm_entry_myself' );
    add_action( 'wp_ajax_nopriv_ocm_entry_myself', 'ocm_entry_myself' );

    function ocm_entry_myself(){
        $errors = array();
        $has_errors = false;
        $reset_result = false;
        $success = false;
       
		$formData = isset($_POST['formData']) ? $_POST['formData'] : '';
        $form = ds_unserialize_form($formData);

        $group = '';
        $event_id = 0;
        $event_columns = array();

        if(isset($form['group'])){
            $group = trim(urldecode($form['group']));
        }

        if(isset($form['event_id'])){
            $event_id = trim($form['event_id']);
        }

        if(isset($form['event_columns'])){
            $event_columns = explode(',',urldecode($form['event_columns']));
        }
        $group_number = 3 + array_search('Группа', $event_columns);

        // Let's do some error handling
        if(!$group){
            $has_errors = true;
            $errors[] = __('Category is not chosen', TEXT_DOMAIN);
        }
        $entries = get_post_meta($event_id, 'entry', true) ?: array();
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        
        if ( in_array($user_id, array_column($entries, 'id')) ) {
            $has_errors = true;
            $errors[] = __('You have been already entered this event', TEXT_DOMAIN);
        }
        
        // Let's trigger the call
        if(!$has_errors) {
            $entry = array();
            $icons = '';

            if ( user_can($current_user->ID,'edit_posts') ) $icons .= get_edit_button($event_id, $group_number);
            if ( user_can($current_user->ID,'delete_posts') ) $icons .= get_delete_button($event_id);
            $entry['icons'] = $icons;

            $entry['id'] = $user_id;
            foreach ($event_columns as $column) {
                $entry[$column] = $column == 'Группа' ? $group : get_user_entry_data($column);
            }
            
            $entries[]=$entry;
            update_post_meta($event_id, 'entry', $entries);
            $success = true;
            // $reset_result = $DS_Loya_Account->ds_reset_user_password($policy_number, $last_name, $dob, $zipcode, $email, $password);
        }

        // ds_write_log($reset_result);
	
	    $return = array(
            'event_columns' => $event_columns,
            'entries' => $entries,
		    'entry' => $entry,
            'has_errors' => $has_errors,
		    'errors' => $errors,
            'success' => $success
		);

	    wp_send_json($return);
    }

    function ds_unserialize_form($str) {
        $returndata = array();
        $strArray = explode("&", $str);
        $i = 0;
        foreach ($strArray as $item) {
            $array = explode("=", $item);
            
            if (preg_match('/(%5B%5D)/', $array[0])) {
                  $array[0] = str_replace('%5B%5D','',$array[0]);
                  if(array_key_exists($array[0],$returndata)){
                          $returndata[$array[0]][]=$array[1];
                  }else{
                      $returndata[$array[0]] =array();
                      $returndata[$array[0]][]=$array[1];
                  }
            }else
            {
                $returndata[$array[0]] = $array[1];
            }
        }
        
        return $returndata;
    }
}