( function ( $ ) {
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

    $(document).ready(function () {
       
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

    });

} )( jQuery );