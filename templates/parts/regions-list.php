<?php
/**
 * Use to create and display list of regions with redirect to view sheet page
 */

use XRG\RD\XrgHelperFunctions;

$regionalData = get_option('xrg_regional_data');
?>

<!--  FILE (HR FILE) UPLOAD BUTTON  -->
<div class="button-wrapper">
    <div class="btn-container button">
        <a href="<?php echo site_url('/rd-file-upload'); ?>" class="btn">Upload New Hire File</a>
    </div>
</div>

<div class="flex-container-locations">
    <div class="flex-head">
        <div class="flex-col-form-head">
            <span class="heading_text">Region Name</span>
        </div>
        <div class="flex-col-form-head">
            <span class="heading_text">Data Entry Sheet</span>
        </div>
        <div class="flex-col-form-head">
            <span class="heading_text">View Sheet</span>
        </div>
    </div>
    <?php foreach($regionalData as $region) : ?>
    <div class="flex-body">
        <div class="flex-col-form">
            <span class="field_val">
                <a href="<?php echo esc_url(site_url('/rd-view-sheet/?region=' . $region['region_name'])); ?>"><?php echo esc_html($region['region_name']); ?></a>
            </span>
        </div>
        <div class="flex-col-form">
            <span class="field_val">
                <a href="<?php echo esc_url(site_url('/rd-entry-sheet/?region=' . $region['region_name'])); ?>" title="Data Entry Sheet" class="xrg-action-link">
                    <?php echo XrgHelperFunctions::xrgSvgLoader(XRG_PLUGIN_PATH . 'assets/imgs/entry-icon.svg'); ?>
                </a>
            </span>
        </div>
        <div class="flex-col-form">
            <span class="field_val">
                <a href="<?php echo esc_url(site_url('/rd-view-sheet/?region=' . $region['region_name'])); ?>" title="Data Entry Sheet" class="xrg-action-link">
                <?php echo XrgHelperFunctions::xrgSvgLoader(XRG_PLUGIN_PATH . 'assets/imgs/view_sheet_icon.svg'); ?>
                </a>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
