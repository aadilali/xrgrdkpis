( function ( $ ) {
    // Function to Add new locations HTML
    const addNewLocationHTML = (thisObj) => {
        // Get the current Index of data array by data attribute
        let currentIndex = $(thisObj).data('current-index');
        
        let locationHTML = "<div class='field-container'><label>Location Name: </label><input name='xrg_regional_data[" + currentIndex + "][locations][]' type='text' /></div>";

        $(locationHTML).insertBefore(thisObj);
    }

    // Function to Add new Regions HTML
    const addNewRegionHTML = (thisObj) => {
        // Get the current Index of data array by data attribute
        let currentIndex = $(thisObj).data('current-index');
        
        let regionHTML = "<div class='region-container'><label>Region Name: </label><input name='xrg_regional_data[" + currentIndex + "][region_name]' type='text' /><div class='location-container'><label>Location Name: </label><input name='xrg_regional_data[" + currentIndex + "][locations][]' type='text' /><div class='button button-secondary add-location-btn' data-current-index=" + currentIndex +">New Location</div></div></div>";
        let placementLocation = $(thisObj).prev();
        $(placementLocation).append(regionHTML);
        currentIndex += 1;
        $(thisObj).data('current-index', currentIndex);
    }

     // Function to delete region box
     const delRegion = (thisObj) => {
        // Get confirmation from user
        if (confirm("Are you sure to delete this Region?")) {
            $(thisObj).parent('.region-container').remove(); 
        }
    }

    // Function to delete Location box
    const delLocation = (thisObj) => {
        // Get confirmation from user
        if (confirm("Are you sure to delete this Location?")) {
            $(thisObj).parent('.field-container').remove(); 
        }
    }

    $(document).ready(function () {
        $(document).on('click', '.add-location-btn', function () {
            addNewLocationHTML(this);
        });

        $(document).on('click', '.add-region-btn', function () {
            addNewRegionHTML(this);
        });

        $(document).on('click', '.del-region-btn', function () {
            delRegion(this);
        });

        $(document).on('click', '.del-location-btn', function () {
            delLocation(this);
        });

    });

} )( jQuery );