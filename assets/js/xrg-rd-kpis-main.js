( function ( $ ) {
    // Function to display the Tabs content
    const openCity = (thisObj) => {
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
            openCity(this)
        } );

         // Get the element with id="defaultOpen" and click on it
         $(".periods-tab").last().click();

    });
      
    
    
      
} )( jQuery );