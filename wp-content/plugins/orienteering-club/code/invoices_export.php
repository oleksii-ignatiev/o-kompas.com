<?php 

    $ocm_any_export_hidden = isset($_POST['ocm_any_export_hidden']) ? 'yes' : 'no';    
    $ocm_invoice_export_hidden = isset($_POST['ocm_invoice_export_hidden']) ? 'yes' : 'no';    
    $ocm_invoice_export_category = isset($_POST['ocm_invoice_category']) ? esc_attr( $_POST['ocm_invoice_category'] ) : -1;

    if ( $ocm_invoice_export_hidden == 'yes' ) {
        header("Content-type: text/csv;");
        header("Content-Disposition: attachment; filename=" . __('Invoices', 'o-kompas') . ".csv");

        ocm_invoice_list( $ocm_invoice_export_category );

        exit();
    } else if ( $ocm_any_export_hidden == 'yes' ) {
        // other export in progress
        return;
    }
?>

<div class="wrap">

<form name="ocm_invoice_export_form" method="post" action="<?php echo admin_url('admin.php').'?page='.$_GET['page'].'&noheader=true'; ?>">
    <div class="metabox-holder ">
        <div class="postbox"> 
            <h3><?php _e('Export Invoices', 'o-kompas') ?></h3>
        </div>
    </div>
<?php
    echo apply_filters( 'ocm_invoices_export_selection', "" );
?>        
    <input type="submit" name="submit_invoice_export" class="button-primary" value="<?php _e('Export Invoices to File', 'o-kompas')?>" />
    <input type="hidden" name="ocm_invoice_export_hidden" value="yes">
    <input type="hidden" name="ocm_any_export_hidden" value="yes">
</form>

</div>
