<?php

use XRG\RD\XrgRdKpis;
use XRG\RD\XrgHelperFunctions;

get_header();

// KPIs Form Data
$retMessage = '';
if( isset( $_POST['xrg_file_data_submit'] ) && $_POST['xrg_file_data_submit'] === 'UPLOAD' ) {
    
    //Verify Nounce
    if ( isset( $_POST['xrg_verify_file'] ) || wp_verify_nonce( $_POST['xrg_verify_file'], 'xrg_verify_file_data' ) ) {
        
        unset( $_POST['xrg_file_data_submit'] );
        $retMessage = 'File Upload failed please try again! (file extension should be .xlsx)';

        if(XrgHelperFunctions::xrgHandleFileUpload($_FILES)) {
            $retMessage = 'File Uploaded successfully!';
        }
    }
}
?>
<div class="alignwide xrg-wrapper">
    <div class="flex-container-form xrg-file-data">
        <!-- File Upload Form  -->
        <div id="file_data_form">
            <h2>Upload XLSX file having employees data</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field( 'xrg_verify_file_data', 'xrg_verify_file' ); ?>
                <div class="flex-body">
                    <div class="flex-col-form">
                        <span class="heading_text">
                            Select File to Upload
                        </span> 
                        <span class="field_val">
                            <input type="file" name="xrg_file" />
                            <span class="full-width input-description">Please select .xlsx excel file to upload, having employees data</span>
                        </span>
                    </div>
                </div>
                <div class="flex-button-body">
                    <div class="flex-col-form border-none">
                        <span class="notification">
                            <?php echo $retMessage; ?>
                        </span>
                    </div>
                    <div class="flex-col-form border-none align-right">
                        <input type="submit" id="save_record" class="btn" name="xrg_file_data_submit" value="UPLOAD" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
get_footer();
