<div class="wrap">

<?php 
    $ocm_invoice_option_hidden = isset($_POST['ocm_invoice_option_hidden']) ? 'yes' : 'no';    

    if ( $ocm_invoice_option_hidden == 'yes' ) {
        
        $include_invoices = isset($_POST['ocm_include_invoices_data']) ? esc_attr($_POST['ocm_include_invoices_data']) : '';
        update_option('ocm_include_invoices_data', $include_invoices);
        
		$custom1_invoice = isset($_POST['ocm_custom1_invoice']) ? esc_attr($_POST['ocm_custom1_invoice']) : '';
		update_option('ocm_custom1_invoice', $custom1_invoice);
		$custom2_invoice = isset($_POST['ocm_custom2_invoice']) ? esc_attr($_POST['ocm_custom2_invoice']) : '';
		update_option('ocm_custom2_invoice', $custom2_invoice);
        
        do_action('ocm_invoices_options_save', $_POST);
            
?>
<div class="updated">
    <p><strong><?php _e('Invoice options saved.', TEXT_DOMAIN); ?></strong></p>
</div>          
<?php 

    } else {
    	$include_invoices = get_option('ocm_include_invoices_data');
        
        $custom1_invoice = get_option('ocm_custom1_invoice');
        $custom2_invoice = get_option('ocm_custom2_invoice');
    }
    
    if ($include_invoices == 'use_invoices') {
        $include_invoices_checked = "checked";
    } else {
        $include_invoices_checked = "";
    } 
    
?>

<form name="ocm_invoice_option_form" method="post" action="<?php echo admin_url('admin.php').'?page='.$_GET['page']; ?>">
    <div class="metabox-holder ">
        <div class="postbox"> 
            <h3><?php _e('Invoice Options', TEXT_DOMAIN) ?></h3>
        </div>
    </div>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Include invoices', TEXT_DOMAIN) ?></th>
            <td> <fieldset><legend class="screen-reader-text"></legend><label for="include_invoices">
            <input name="ocm_include_invoices_data" type="checkbox" value="use_invoices" <?php echo $include_invoices_checked; ?> />
            <?php _e('Granted', TEXT_DOMAIN) ?></label>
            </fieldset></td>
        </tr>
        <tr>
            <th scope="row"><label for="custom_invoice"><?php _e('Custom values', TEXT_DOMAIN) ?></label></th>
            <td>
            <input name="ocm_custom1_invoice" type="text" value="<?php echo $custom1_invoice; ?>" class="regular-text" />
            <p class="description"><?php _e('Name for custom value #1', TEXT_DOMAIN) ?></p>
            <input name="ocm_custom2_invoice" type="text" value="<?php echo $custom2_invoice; ?>" class="regular-text" />
            <p class="description"><?php _e('Name for custom value #2', TEXT_DOMAIN) ?></p>
            </td>
        </tr>
<?php
    echo apply_filters( 'ocm_invoices_options_display', "" );
?>        
    </table>
    <input type="submit" name="submit_invoice_option" class="button-primary" value="<?php _e('Save Invoice Options', TEXT_DOMAIN) ?>" />
    <input type="hidden" name="ocm_invoice_option_hidden" value="yes">
</form>

</div>
