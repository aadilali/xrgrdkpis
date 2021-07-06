<?php

//use BrunchSheet\BrunchData;
//(BrunchData::instance());
//echo BrunchData::instance()->getData();

get_header();
?>
<div class="alignwide">
    <?php echo "TEST PAGE FOR ROUTE 1"; ?>
    <form method="post" action="" id="kpi_sheet">
        <div class="flex-head">
            <div class="flex-col-form-head mw-70">
                <span class="heading_text">SI No</span>
            </div>
            <div class="flex-col-form-head mw-100">
                <span class="heading_text">Micro Check #</span>
            </div>
            <div class="flex-col-form-head">
                <span class="heading_text">Final Table #</span> 
            </div>
            <div class="flex-col-form-head mw-100">
                <span class="heading_text">Seated Time</span> 
            </div>
            <div class="flex-col-form-head mw-70">
                <span class="heading_text">Adult Count (+iv)</span> 
            </div>
            <div class="flex-col-form-head mw-70">
                <span class="heading_text">Kids Count (+iv)</span> 
            </div>
        </div>
        <div class="flex-body">
            <div class="flex-col-form mw-70">
                <span class="field_val">
                    1
                </span>
            </div>
            <div class="flex-col-form mw-100">
                <span class="field_val">
                    <input type="text" name="micro_check[]" class="micro-check" placeholder="Micro Check #" />
                </span>
            </div>
            <div class="flex-col-form">
                <span class="field_val">
                    <input type="text" name="final_table[]" class="final-table" placeholder="Final Table #" />
                </span> 
            </div>
            <div class="flex-col-form mw-100">
                <span class="field_val">
                    <input type="text" class="timepicker" name="seated_time[]" placeholder="Seated Time" autocomplete="off" />
                </span> 
            </div>
            <div class="flex-col-form mw-70">
                <span class="field_val">
                    <input type="text" name="adult_count[]" placeholder="Adult Count +iv" value="0" />
                </span> 
            </div>
            <div class="flex-col-form mw-70">
                <span class="field_val">
                    <input type="text" name="kids_count[]" placeholder="Kids Count +iv" value="0" />
                </span>
            </div>
        </div>
        
        <div class="flex-button-body">
            <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="control_sheet_submit" value="SAVE" /></div>
        </div>
    </form>
</div>
<?php
get_footer();
