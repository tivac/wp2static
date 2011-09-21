jQuery(document).ready(function($) {
    
    $(".export").click(function() {
        var iframe = $('#wp2static-results iframe');
        
        //build iframe first click, later clicks simply reload it
        if(!iframe.length) {
            $('<iframe />', {
                src : ajaxurl + "?action=wp2static_export"
            }).appendTo('#wp2static-results');
        } else {
            iframe[0].contentDocument.location.reload(true);
        }
        
        return false;
    });
});
