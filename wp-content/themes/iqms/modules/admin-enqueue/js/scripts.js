var app = {

    limitTerm:function(){
        jQuery('#documents_labelchecklist input[type="checkbox"]').on('change', function(event){
            var checked = jQuery(this).prop('checked');
            // Clear all other inputs on this level:
            jQuery(this).closest('ul').find('input[type="checkbox"]').removeAttr('checked');
            // Set the value:
            jQuery(this).prop('checked', checked);
        })
    },
    addViewFileLink: function() {
        if ( jQuery('body').hasClass('post-type-dcm') && jQuery('form#post').length ) {
            $link = jQuery('#sample-permalink a').attr('href');
            jQuery('[data-name="upload_document"] label').append( ' <a href="'+$link+'" target="_blank">View File</a>' );
            jQuery('[data-name="file_url"] label').append( ' <a href="'+$link+'" target="_blank">View File</a>' );
        }
    }

}
jQuery(document).ready(function(){
    app.limitTerm();
    app.addViewFileLink();

    jQuery('#post').submit(function(e) {
        if (jQuery('#title').val().trim() === '') {
            alert('The title is required.');
            jQuery('#title').focus();
            return false;
        }
    });
});
