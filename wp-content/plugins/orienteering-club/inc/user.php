<?php

// for account owner
add_action('show_user_profile', 'add_custom_user_profile_fields');
add_action('personal_options_update', 'save_custom_user_profile_fields');

// for admins
add_action('edit_user_profile', 'add_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');

add_filter( 'user_contactmethods', 'add_user_details' );

function add_user_details( $method ) {

	$custom_contact = [
		'phone'   => __( 'Phone', TEXT_DOMAIN ),
		'city'    => __( 'City', TEXT_DOMAIN ),
		'country' => __( 'Country', TEXT_DOMAIN ),
	];

	$method = array_merge( $method, $custom_contact );

	return $method;

}

function add_custom_user_profile_fields( $user ) {
    global $qualifications;

    $months 	= array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
    $default	= array( 'day' => 1, 'month' => 'Jnuary', 'year' => 1950, );
    $birth_date = wp_parse_args( get_the_author_meta( 'birth_date', $user->ID ), $default );

    $club = get_the_author_meta( 'club', $user->ID ) ?: '';
    $club_args = array(
        'post_type' => 'clubs',
        'posts_per_page' => -1,
        'orderby' => 'name',
        'order' => 'ASC',
    );

    $rank = get_the_author_meta( 'qualification', $user->ID ) ?: '0';

    $si_number = get_the_author_meta( 'si_number', $user->ID ) ?: '';
    $community = get_the_author_meta( 'community', $user->ID ) ?: '';

    // $rank = get_the_author_meta( 'qualification', $user->ID ) ?: '0';
    
    ?>
    <h3><?php _e('User Data For Entries', TEXT_DOMAIN); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="birth-date-day"><?php _e('Date Of Birth', TEXT_DOMAIN); ?></label></th>
            <td>
                <select id="birth-date-day" name="birth_date[day]"><?php
                    for ( $i = 1; $i <= 31; $i++ ) {
                        printf( '<option value="%1$s" %2$s>%1$s</option>', $i, selected( $birth_date['day'], $i, false ) );
                    }
                ?></select>
                <select id="birth-date-month" name="birth_date[month]"><?php
                    foreach ( $months as $month ) {
                        printf( '<option value="%1$s" %2$s>%1$s</option>', $month, selected( $birth_date['month'], $month, false ) );
                    }
                ?></select>
                <select id="birth-date-year" name="birth_date[year]"><?php
                    for ( $i = 1900; $i <= date('Y'); $i++ ) {
                        printf( '<option value="%1$s" %2$s>%1$s</option>', $i, selected( $birth_date['year'], $i, false ) );
                    }
                ?></select>
            </td>

        </tr>
        <tr>
            <th><label for="club"><?php _e('Club', TEXT_DOMAIN); ?></label></th>
            <td>
                <select id='club' name='club'>
                    <?php foreach ( get_posts($club_args) as $current_club ) {
                        printf( '<option value="%1$s" %2$s>%3$s</option>', $current_club->post_title, selected( $club, $current_club->post_title, false ), $current_club->post_title );
                    } ?>
                </select>            
            </td>
        </tr>
        <tr>
            <th><label for="qualification"><?php _e('Qualification', TEXT_DOMAIN); ?></label></th>
            <td>
                <select id='qualification' name='qualification'>
                    <?php foreach ($qualifications as $key => $qualification) {
                        printf( '<option value="%1$s" %2$s>%3$s</option>', $key, selected( $rank, $key, false ), $qualification );
                    } ?>
                </select>            
            </td>
        </tr>
        <tr>
            <th><label for="birth-date-day"><?php _e('SI Number', TEXT_DOMAIN); ?></label></th>
            <td>
                <input id='si_number' name='si_number' value="<?php echo $si_number; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="birth-date-day"><?php _e('Sport Community', TEXT_DOMAIN); ?></label></th>
            <td>
                <input id='community' name='community' value="<?php echo $community; ?>" />
            </td>
        </tr>
    </table>
    <?php
}

function save_custom_user_profile_fields( $user_id ) {

    if ( ! current_user_can( 'edit_user', $user_id ) ) {
   	    return false;
    }

    if ( !empty( $_POST['birth_date'] ) ) {
   	    update_usermeta( $user_id, 'birth_date', $_POST['birth_date'] );
    }

    if ( !empty( $_POST['club'] ) ) {
   	    update_usermeta( $user_id, 'club', $_POST['club'] );
    }

    if ( !empty( $_POST['qualification'] ) ) {
   	    update_usermeta( $user_id, 'qualification', $_POST['qualification'] );
    }

    if ( !empty( $_POST['si_number'] ) ) {
   	    update_usermeta( $user_id, 'si_number', $_POST['si_number'] );
    }

    if ( !empty( $_POST['community'] ) ) {
   	    update_usermeta( $user_id, 'community', $_POST['community'] );
    }
}

function ses_add_users_list_to_contact_form ( $tag, $unused ) {  
    global $qualifications;
       
    if ( $tag['name'] != 'users' )  
        return $tag;

    $current_user = wp_get_current_user();
    $current_club = get_the_author_meta( 'club', $current_user->ID );
    if ( !in_array( 'coach', $current_user->roles ) ) {
        
        $tag['raw_values'][] = $current_user->display_name;  
        $tag['values'][] = $current_user->display_name;  
        $tag['labels'][] = $current_user->display_name; 
        
        return $tag;
    }
  
    $args = array ( 
        'orderby' => 'last_name',  
        'order' => 'ASC',
        'meta_key' => 'club', 
        'meta_value' => get_the_author_meta( 'club', $current_user->ID ),
    );  
    $users = get_users($args);  
  
    if ( ! $users )  
        return $tag;  
  
    foreach ( $users as $user ) {
        
        $birth_date = get_the_author_meta( 'birth_date', $user->ID );
        $club = get_the_author_meta( 'club', $user->ID );
        $qualification = $qualifications[ (int)get_the_author_meta( 'qualification', $user->ID ) ];
        $si_number = get_the_author_meta( 'si_number', $user->ID );
        $community = get_the_author_meta( 'community', $user->ID );
        // echo '<pre>';print_r($user_details);echo '</pre>';
        $tag['raw_values'][] = $user->display_name .' '. $si_number .' '. $qualification .' '. $club .' '. $community;  
        $tag['values'][] = $user->display_name .' '. $si_number .' '. $qualification .' '. $club .' '. $community;  
        $tag['labels'][] = $user->display_name .' '. $si_number .' '. $qualification .' '. $club .' '. $community;  
        // $tag['pipes']->pipes[] = array ( 'before' => $user->last_name, 'after' => $user->last_name);  
    }  
  
    return $tag;  
}  
add_filter( 'wpcf7_form_tag', 'ses_add_users_list_to_contact_form', 10, 2); 
