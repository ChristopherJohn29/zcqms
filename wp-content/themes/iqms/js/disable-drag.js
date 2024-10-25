jQuery(document).ready(function($) {
    // Function to remove sortable
    function disableSortable() {
        $('.meta-box-sortables').sortable('destroy');
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
});