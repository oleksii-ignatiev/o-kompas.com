<?php 

    $ocm_any_export_hidden = isset($_POST['ocm_any_export_hidden']) ? 'yes' : 'no';    
    $ocm_member_export_hidden = isset($_POST['ocm_member_export_hidden']) ? 'yes' : 'no'; 
	$ocm_member_export_columns = isset($_POST['ocm_member_columns']) ? $_POST['ocm_member_columns'] : array();
    $ocm_member_export_category = isset($_POST['ocm_member_category']) ? esc_attr( $_POST['ocm_member_category'] ) : -1;

    if ( $ocm_member_export_hidden == 'yes' ) { 
		
		header("Content-type: text/csv;");
        header("Content-Disposition: attachment; filename=" . __('Members', 'o-kompas') . ".csv"); 
        
		if ( $ocm_member_export_columns == "all_columns" ) {
			$ocm_member_export_columns = array( 
                __('Date of birth', 'o-kompas'), __('Age', 'o-kompas'),
                __('Gender', 'o-kompas'), __('Level', 'o-kompas'),
                __('Start date membership', 'o-kompas'), __('End date membership', 'o-kompas'), 
                get_option('ocm_custom1_member') ?: "[Custom1]",
                get_option('ocm_custom2_member') ?: "[Custom2]",
                get_option('ocm_custom3_member') ?: "[Custom3]",
                get_option('ocm_custom4_member') ?: "[Custom4]",
                __('User name', 'o-kompas'),		
            );
		}
        ocm_member_list( $ocm_member_export_columns, $ocm_member_export_category );

        exit();
    } else if ( $ocm_any_export_hidden == 'yes' ) {
        // other export in progress
        return;
	}
?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br/></div>
    <h2><span class="dashicons dashicons-media-text"></span> <?php _e('Orienteering Club Management', 'o-kompas')?> - <?php _e('Export Data', 'o-kompas')?></h2>
</div>

<div class="wrap">

<form name="ocm_member_export_form" method="post" action="<?php echo admin_url('admin.php').'?page='.$_GET['page'].'&noheader=true'; ?>">
    <div class="metabox-holder ">
        <div class="postbox"> 
            <h3><?php _e('Export Members', 'o-kompas') ?></h3>
        </div>
    </div>
<?php
    echo apply_filters( 'ocm_members_export_selection', "<input type='hidden' name='ocm_member_columns' value='all_columns'>" );
?>        
    <input type="submit" name="submit_member_export" class="button-primary" value="<?php _e('Export Members to File', 'o-kompas')?>" />
    <input type="hidden" name="ocm_member_export_hidden" value="yes">
    <input type="hidden" name="ocm_any_export_hidden" value="yes">
</form>

</div>