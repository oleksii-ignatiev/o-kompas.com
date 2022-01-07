<div class="wrap">

<?php 
    $ocm_member_option_hidden = isset($_POST['ocm_member_option_hidden']) ? 'yes' : 'no';    

    if ( $ocm_member_option_hidden == 'yes' ) {
    
        $display_privacy = isset($_POST['ocm_display_privacy_data']) ? esc_attr($_POST['ocm_display_privacy_data']) : '';
        update_option('ocm_display_privacy_data', $display_privacy);
        $display_addrmailphone_privacy = isset($_POST['ocm_display_addrmailphone_privacy_data']) ? esc_attr($_POST['ocm_display_addrmailphone_privacy_data']) : '';
        update_option('ocm_display_addrmailphone_privacy_data', $display_addrmailphone_privacy);
        
		$custom1_member = isset($_POST['ocm_custom1_member']) ? esc_attr($_POST['ocm_custom1_member']) : '';
		update_option('ocm_custom1_member', $custom1_member);
		$custom2_member = isset($_POST['ocm_custom2_member']) ? esc_attr($_POST['ocm_custom2_member']) : '';
		update_option('ocm_custom2_member', $custom2_member);
		$custom3_member = isset($_POST['ocm_custom3_member']) ? esc_attr($_POST['ocm_custom3_member']) : '';
		update_option('ocm_custom3_member', $custom3_member);
		$custom4_member = isset($_POST['ocm_custom4_member']) ? esc_attr($_POST['ocm_custom4_member']) : '';
		update_option('ocm_custom4_member', $custom4_member);
        
        do_action('ocm_members_options_save', $_POST);
            
?>
<div class="updated">
    <p><strong><?php _e('Member options saved.', TEXT_DOMAIN); ?></strong></p>
</div>
<?php 

    } else {
    	$display_privacy = get_option('ocm_display_privacy_data');
    	$display_addrmailphone_privacy = get_option('ocm_display_addrmailphone_privacy_data');
        
        $custom1_member = get_option('ocm_custom1_member');
        $custom2_member = get_option('ocm_custom2_member');
        $custom3_member = get_option('ocm_custom3_member');
        $custom4_member = get_option('ocm_custom4_member');
    }
    
    if ($display_privacy == 'no_privacy') {
        $display_privacy_checked = "checked";
    } else {
        $display_privacy_checked = "";
    }
    if ($display_addrmailphone_privacy == 'no_addrmailphone_privacy') {
        $display_addrmailphone_privacy_checked = "checked";
    } else {
        $display_addrmailphone_privacy_checked = "";
    }
    
?>

<form name="ocm_member_option_form" method="post" action="<?php echo admin_url('admin.php').'?page='.$_GET['page']; ?>">
    <div class="metabox-holder ">
        <div class="postbox"> 
            <h3><?php _e('Member Options', TEXT_DOMAIN) ?></h3>
        </div>
    </div>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Privacy', TEXT_DOMAIN) ?></th>
            <td> 
			<fieldset><legend class="screen-reader-text"></legend><label for="display_privacy">
            <input name="ocm_display_privacy_data" type="checkbox" value="no_privacy" <?php echo $display_privacy_checked; ?> />
            <?php _e('Display privacy related data of other members', TEXT_DOMAIN) ?></label>
            </fieldset>
            <fieldset><legend class="screen-reader-text"></legend><label for="display_addrmailphone_privacy">
            <input name="ocm_display_addrmailphone_privacy_data" type="checkbox" value="no_addrmailphone_privacy" <?php echo $display_addrmailphone_privacy_checked; ?> />
            <?php _e('Display address, e-mail, and phone numbers of other members', TEXT_DOMAIN) ?></label>
            </fieldset>
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="custom_member"><?php _e('Custom values', TEXT_DOMAIN) ?></label></th>
            <td>
            <input name="ocm_custom1_member" type="text" value="<?php echo $custom1_member; ?>" class="regular-text" />
            <p class="description"><?php _e('Name for custom value #1', TEXT_DOMAIN) ?></p>
            <input name="ocm_custom2_member" type="text" value="<?php echo $custom2_member; ?>" class="regular-text" />
            <p class="description"><?php _e('Name for custom value #2', TEXT_DOMAIN) ?></p>
            <input name="ocm_custom3_member" type="text" value="<?php echo $custom3_member; ?>" class="regular-text" />
            <p class="description"><?php _e('Name for custom value #3', TEXT_DOMAIN) ?></p>
            <input name="ocm_custom4_member" type="text" value="<?php echo $custom4_member; ?>" class="regular-text" />
            <p class="description"><?php _e('Name for custom value #4', TEXT_DOMAIN) ?></p>
            </td>
        </tr>
<?php
    echo apply_filters( 'ocm_members_options_display', "" );
?>        
    </table>
    <input type="submit" name="submit_member_option" class="button-primary" value="<?php _e('Save Member Options', TEXT_DOMAIN) ?>" />
    <input type="hidden" name="ocm_member_option_hidden" value="yes">
</form>

</div>
