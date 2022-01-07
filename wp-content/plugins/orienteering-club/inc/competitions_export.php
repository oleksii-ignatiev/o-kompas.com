<?php 

    $ocm_any_export_hidden = isset($_POST['ocm_any_export_hidden']) ? 'yes' : 'no';    
    $ocm_competition_export_hidden = isset($_POST['ocm_competition_export_hidden']) ? 'yes' : 'no';    
    $ocm_competition_export_category = isset($_POST['ocm_comp_category']) ? esc_attr( $_POST['ocm_comp_category'] ) : -1;

    if ( $ocm_competition_export_hidden == 'yes' ) {
        header("Content-type: text/csv;");
        header("Content-Disposition: attachment; filename=" . __('Matches', TEXT_DOMAIN) . ".csv");

        ocm_competition_list( $ocm_competition_export_category );

        exit();
    } else if ( $ocm_any_export_hidden == 'yes' ) {
        // other export in progress
        return;
    }
?>

<div class="wrap">

<form name="ocm_competition_export_form" method="post" action="<?php echo admin_url('admin.php').'?page='.$_GET['page'].'&noheader=true'; ?>">
    <div class="metabox-holder ">
        <div class="postbox"> 
            <h3><?php _e('Export Competitions', TEXT_DOMAIN) ?></h3>
        </div>
    </div>
<?php
    echo apply_filters( 'ocm_competitions_export_selection', "" );
?>        
    <input type="submit" name="submit_competition_export" class="button-primary" value="<?php _e('Export Competitions to File', TEXT_DOMAIN)?>" />
    <input type="hidden" name="ocm_competition_export_hidden" value="yes">
    <input type="hidden" name="ocm_any_export_hidden" value="yes">
</form>

</div>
