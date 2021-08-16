<?php
/**
 * Use to create and display list of regions with redirect to view sheet page
 */

$regionalData = get_option('xrg_regional_data');
?>

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
                    <svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" viewBox="0 0 8.4666665 10.583325375" version="1.1" x="0px" y="0px"><g transform="translate(-43.192356,-263.21989)" style="" display="inline"><path style="color:#000000;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:medium;line-height:normal;font-family:sans-serif;font-variant-ligatures:normal;font-variant-position:normal;font-variant-caps:normal;font-variant-numeric:normal;font-variant-alternates:normal;font-variant-east-asian:normal;font-feature-settings:normal;font-variation-settings:normal;text-indent:0;text-align:start;text-decoration:none;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#000000;letter-spacing:normal;word-spacing:normal;text-transform:none;writing-mode:lr-tb;direction:ltr;text-orientation:mixed;dominant-baseline:auto;baseline-shift:baseline;text-anchor:start;white-space:normal;shape-padding:0;shape-margin:0;inline-size:0;clip-rule:nonzero;display:inline;overflow:visible;visibility:visible;opacity:1;isolation:auto;mix-blend-mode:normal;color-interpolation:sRGB;color-interpolation-filters:linearRGB;solid-color:#000000;solid-opacity:1;vector-effect:none;fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.264583;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;color-rendering:auto;image-rendering:auto;shape-rendering:auto;text-rendering:auto;enable-background:accumulate;stop-color:#000000" d="m 47.028858,270.36362 a 0.1322915,0.1322915 0 0 0 -0.132324,0.13233 v 0.79375 a 0.13230472,0.13230472 0 0 0 0.132324,0.13232 h 0.79375 a 0.13230472,0.13230472 0 0 0 0.132227,-0.13232 v -0.79375 a 0.1322915,0.1322915 0 0 0 -0.132227,-0.13233 0.1322915,0.1322915 0 0 0 -0.132324,0.13233 v 0.66142 h -0.529199 v -0.66142 a 0.1322915,0.1322915 0 0 0 -0.132227,-0.13233 z m -3.439649,0.79375 a 0.1322915,0.1322915 0 0 0 -0.132226,0.13233 0.1322915,0.1322915 0 0 0 0.132226,0.13222 l 7.67295,10e-5 a 0.1322915,0.1322915 0 0 0 0.132324,-0.13232 0.1322915,0.1322915 0 0 0 -0.132324,-0.13232 z m 2.38125,-4.49785 c -0.588212,0 -1.119432,0.3549 -1.344531,0.89834 -0.225099,0.54344 -0.100402,1.16991 0.315528,1.58584 0.415929,0.41593 1.042402,0.54063 1.585839,0.31553 0.543438,-0.2251 0.898341,-0.75632 0.89834,-1.34453 a 0.13230472,0.13230472 0 0 0 -0.132226,-0.13223 h -1.190723 v -1.19072 a 0.13230472,0.13230472 0 0 0 -0.132227,-0.13223 z m -0.132324,0.27442 v 1.18076 a 0.13230472,0.13230472 0 0 0 0.132324,0.13232 h 1.180762 c -0.04776,0.42749 -0.321173,0.80046 -0.725097,0.96778 -0.445272,0.18443 -0.956861,0.0827 -1.297657,-0.25811 -0.340796,-0.3408 -0.442543,-0.85238 -0.258105,-1.29766 0.167311,-0.40392 0.540281,-0.67733 0.967773,-0.72509 z m 0.926075,-1.06817 c -0.09721,0 -0.132232,0.0588 -0.132232,0.13223 v 1.32295 c -1.1e-5,0.073 0.05918,0.13228 0.132226,0.13232 h 1.322949 c 0.07309,1e-5 0.132336,-0.0592 0.132325,-0.13232 0,-0.79297 -0.638986,-1.43776 -1.428516,-1.45245 -0.0088,-0.002 -0.01777,-0.003 -0.02676,-0.003 z m 0.132324,0.27783 c 0.551646,0.0614 0.9836,0.49337 1.045019,1.04502 h -1.045019 z m 2.513574,-2.65908 c -0.08409,0 -0.13233,0.0564 -0.13233,0.13223 v 3.96875 c -2e-6,0.0205 0.0048,0.0408 0.01396,0.0592 l 0.529199,1.0583 c 0.04878,0.0974 0.187845,0.0974 0.236622,0 l 0.529199,-1.0583 c 0.0092,-0.0184 0.01397,-0.0386 0.01396,-0.0592 v -3.96875 c -4.3e-5,-0.0731 -0.05928,-0.13224 -0.132325,-0.13223 h -1.055761 c -8.45e-4,-10e-6 -0.0017,-10e-6 -0.0025,0 z m 0.132227,0.26455 h 0.79375 v 3.80518 l -0.396875,0.79385 -0.396875,-0.79385 z m -5.953126,1.32295 a 0.13230523,0.13230523 0 0 0 -0.132226,0.13223 v 5.2917 a 0.13230523,0.13230523 0 0 0 0.132226,0.13232 h 7.67295 a 0.13230523,0.13230523 0 0 0 0.132324,-0.13232 v -5.2917 a 0.13230523,0.13230523 0 0 0 -0.132324,-0.13223 h -0.79375 a 0.13229199,0.13229199 0 0 0 -0.132324,0.13223 0.13229199,0.13229199 0 0 0 0.132324,0.13232 h 0.661426 v 5.02705 h -7.408301 v -5.02705 h 5.688574 a 0.13229199,0.13229199 0 0 0 0.132227,-0.13232 0.13229199,0.13229199 0 0 0 -0.132227,-0.13223 z"/></g></svg>
                </a>
            </span>
        </div>
        <div class="flex-col-form">
            <span class="field_val">
                <a href="<?php echo esc_url(site_url('/rd-view-sheet/?region=' . $region['region_name'])); ?>" title="Data Entry Sheet" class="xrg-action-link">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 66 82.5" enable-background="new 0 0 66 66" xml:space="preserve"><g><g><path d="M3.5,54.1c0,1.6,1.3,3,3,3H23v6h-2.2c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1c6.1,0,18.3,0,24.4,0c0.6,0,1-0.4,1-1    c0-0.6-0.4-1-1-1H43v-6h16.5c1.6,0,3-1.3,3-3V18.6c0-1.6-1.3-3-3-3h-6.6V4c0-1.6-1.3-3-3-3H23.1c-0.2,0-0.5,0.1-0.7,0.3l-9,9    c-0.2,0.2-0.3,0.4-0.3,0.7c0,0,0,0,0,0v4.6H6.5c-1.6,0-3,1.3-3,3C3.5,31.6,3.5,41.4,3.5,54.1z M41,63H25v-6h16V63z M59.5,55    c-18.3,0-34.5,0-53,0c-0.5,0-1-0.4-1-1V49h55v5.1C60.5,54.6,60.1,55,59.5,55z M59.5,17.6c0.5,0,1,0.4,1,1V47h-7.6V17.6H59.5z     M24.1,3h25.9c0.5,0,1,0.4,1,1c0,12,0,35.5,0,43H15.1V12h8h0c0.6,0,1-0.4,1-1V3z M22.1,4.4V10h-5.6L22.1,4.4z M6.5,17.6h6.6V47    H5.5V18.6C5.5,18.1,5.9,17.6,6.5,17.6z"/></g><g><path d="M35.7,51h-5.3c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1h5.3c0.6,0,1-0.4,1-1C36.7,51.5,36.2,51,35.7,51z"/></g><g><path d="M19.6,19.3c0,7.4,0,13.6,0,21.5c0,0.6,0.4,1,1,1c5.9,0,18.9,0,24.9,0c0.6,0,1-0.4,1-1c0-7.9,0-14.1,0-21.5    c0-0.6-0.4-1-1-1c-6,0-18.9,0-24.9,0C20,18.3,19.6,18.7,19.6,19.3z M44.4,32.6H27.9v-5.2h16.5V32.6z M25.9,32.6h-4.4v-5.2h4.4    V32.6z M21.6,34.6h4.4v5.2h-4.4V34.6z M44.4,39.8H27.9v-5.2h16.5V39.8z M44.4,25.4H27.9v-5.2h16.5V25.4z M21.6,20.3h4.4v5.2h-4.4    V20.3z"/></g><g><path d="M30.2,12h14.6c0.6,0,1-0.4,1-1c0-0.6-0.4-1-1-1H30.2c-0.6,0-1,0.4-1,1C29.2,11.5,29.6,12,30.2,12z"/></g></g></svg>
                </a>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
