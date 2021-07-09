<?php

use XRG\RD\XrgRdKpis;
//(BrunchData::instance());
//echo BrunchData::instance()->getData();

get_header();
?>
<div class="alignwide">
    <?php echo "TEST PAGE FOR ROUTE 2"; ?>
    <?php
       // XrgRdKpis::instance()->xrgLoadSpreadSheet()->xrgWriteHtmlTable(); 
       XrgRdKpis::instance()->xrgLoadSpreadSheet()->xrgGenerateSpreadSheet('ASantana');
       /* $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        echo count($rows);*/
    ?>
</div>
<?php
get_footer();
