jQuery(document).ready(function($) {
    // Function to remove sortable
    function disableSortable() {
        $('.meta-box-sortables').sortable('destroy');
        $('.handle-actions').addClass('hidden');
        $('.misc-pub-section').addClass('hidden');
        $('#preview-action').addClass('hidden');
        $('#save-action').css('width',"100%");
    }

    // Disable sortable on page load
    disableSortable();

    // Use MutationObserver to detect changes in the DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if ($(mutation.target).hasClass('meta-box-sortables')) {
                disableSortable();  // Disable sortable again if detected
            }
        });
    });

    // Observe changes in the DOM
    observer.observe(document.body, { childList: true, subtree: true });

    $('#pageparenddiv').remove();
    
    var publishBox = $('#submitdiv');
    if (publishBox.length) {
        // Move the Publish metabox to the lower right
        publishBox.detach().appendTo('#side-sortables');
    }
});