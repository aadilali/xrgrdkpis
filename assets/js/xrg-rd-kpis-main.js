( function ( $ ) {
    // Global variable for storing existing html
    var glbKpisHTML;
    var glbLaborHTML;
    var glbStaffingHTML;
    var staffTabFlag = true;

    // Function to display the Tabs content
    const displayTab = (thisObj) => {
        // Hide All tabs first
        $('.period-tab-content').hide();
        $('.periods-tab').removeClass('active-tab');
        
        // Get Tab ID through data attribute
        let periodName = $(thisObj).data('period-id');
        
        // Show the specific tab content
        $(thisObj).addClass('active-tab');
        $('#'+periodName).show();
    }

    // Function to fecth data from DB using API if Exist
    const getexistinKPIs = (region, period, week) => {

        // initiate loader on front-end screen
        console.log('HERER ', xrgMainObj.ajaxURL);

        // Get data using admin Ajax
        $.ajax({
            type : 'POST',
            url : xrgMainObj.ajaxURL,
            data : {action: 'xrg_kpis_data', 'xrg_region': region, 'xrg_period': period, 'xrg_week': week}
        }).done( function(response) {
            $('#xrg_overlay').css('display', 'none');
            if(response.data_status) {
                $('#kpis_data_container').html(response.res_data);
                return;
            }
            // Else replace existing HTML
            $('#kpis_data_container').html(glbKpisHTML);
        }).fail( function(response) {
            $('#xrg_overlay').css('display', 'none');
            console.log("response FAILED", response);
        });
    }

    // Function to fecth data from DB using API if Exist
    const getexistinLabor = (region, period, week) => {

        // initiate loader on front-end screen
        console.log('LABOR ', xrgMainObj.ajaxURL);

        // Get data using admin Ajax
        $.ajax({
            type : 'POST',
            url : xrgMainObj.ajaxURL,
            data : {action: 'xrg_labor_data', 'xrg_region': region, 'xrg_period': period, 'xrg_week': week}
        }).done( function(response) {
            $('#xrg_overlay').css('display', 'none');
            if(response.data_status) {
                $('#labor_data_container').html(response.res_data);
                return;
            }
            // Else replace existing HTML
            $('#labor_data_container').html(glbLaborHTML);
        }).fail( function(response) {
            $('#xrg_overlay').css('display', 'none');
            console.log("response FAILED", response);
        });
    }

    // Function to fecth data from DB using API if Exist
    const getexistinStaff = region => {

        // initiate loader on front-end screen
        console.log('HERER ', xrgMainObj.ajaxURL);

        // Get data using admin Ajax
        $.ajax({
            type : 'POST',
            url : xrgMainObj.ajaxURL,
            data : {action: 'xrg_staffing_data', 'xrg_region': region}
        }).done( function(response) {
            $('#xrg_overlay').css('display', 'none');
            if(response.data_status) {
                $('#staffing_data_container').html(response.res_data);
                return;
            }
            // Else replace existing HTML
            $('#staffing_data_container').html(glbStaffingHTML);
        }).fail( function(response) {
            $('#xrg_overlay').css('display', 'none');
            console.log("response FAILED", response);
        });
    }

    $(document).ready(function () {

        // Store Kpis and labor HTML
        glbKpisHTML = $('#kpis_data_container').html();
        glbLaborHTML = $('#labor_data_container').html();
        glbStaffingHTML = $('#staffing_data_container').html();

        // Bind All Tabs Link to click function
        $(document).on('click', '.periods-tab', function () {
            displayTab(this)
        } );

        // Get the element with id="defaultOpen" and click on it
        $(".view-template-tabs").last().click();
        $(".active-tab").click();

        // Labor Form week selector
        $('#week-value').text($('.labor-form-week').val());

        $('.labor-form-week').on('change', function () {
           $('#week-value').text($(this).val());
        });

        // On Period change set the week to 'Select' KPIs
        $(document).on('change', '#kpi_sheet select[name="xrg_period"]', function () {

            $(this).parents('#kpi_sheet').find('select[name="xrg_week"').val('');
            $(this).parents('#kpi_sheet').find('select[name="xrg_week"').focus();
            $(this).parents('#kpi_sheet').find('select[name="xrg_week"').css('border', '2px solid #ff0000');
         });

        // On Week change get the data exist in DB  KPIs
        $(document).on('change', '#kpi_sheet select[name="xrg_week"]', function () {

            let exPeriod;
            let exWeek;
            if($(this).val()) {
                exPeriod= $(this).parents('#kpi_sheet').find('select[name="xrg_period"').val();
                exWeek = $(this).val();
                region = $(this).parents('#kpi_sheet').find('input[name="xrg_region"').val();

                // Remove formatting
                $(this).css('border', '1px solid #000');

                // Call function to check and get KPIs data
                $('#xrg_overlay').css('display', 'flex');
                getexistinKPIs(region, exPeriod, exWeek);
            }
         });

        // On Period change set the week to 'Select' Forecast Data
        $(document).on('change', '#labor_sheet select[name="xrg_period"]', function () {

            $(this).parents('#labor_sheet').find('select[name="xrg_week"').val('');
            $(this).parents('#labor_sheet').find('select[name="xrg_week"').focus();
            $(this).parents('#labor_sheet').find('select[name="xrg_week"').css('border', '2px solid #ff0000');
         });

        // On Week change get the data exist in DB  Labor Forecast Data
        $(document).on('change', '#labor_sheet select[name="xrg_week"]', function () {

            let exPeriod;
            let exWeek;
            if($(this).val()) {
                exPeriod= $(this).parents('#labor_sheet').find('select[name="xrg_period"').val();
                exWeek = $(this).val();
                region = $(this).parents('#labor_sheet').find('input[name="xrg_region"').val();

                // Remove formatting
                $(this).css('border', '1px solid #000');

                // Call function to check and get Labor Forecast Data
                $('#xrg_overlay').css('display', 'flex');
                getexistinLabor(region, exPeriod, exWeek);
            }
         });

         $(document).on('click', '#staffing_tab', function() {

            //Check Staffing Pars Data
            
            if(staffTabFlag) {
                $('#xrg_overlay').css('display', 'flex');
                region = $('#staffing_pars_sheet').find('input[name="xrg_region"').val();
                
                getexistinStaff(region);
            }
            staffTabFlag = false;
         });
 
    });

} )( jQuery );