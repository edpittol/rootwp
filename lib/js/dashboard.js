
$j = jQuery.noConflict();

jQuery(document).ready(function(){

    if ( $j( document ).find( '.upload-button' ).length ) root_uploader();

});

function root_uploader()
{
    var input_id;
    var custom_uploader;
    $j( '.upload-button' ).click( function( e ) {
        e.preventDefault();
        input_id = $j( this ).parent().find( '.upload-field' ).attr( 'id' );
        if ( custom_uploader ) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Escolher arquivo',
            button: {
                text: 'Escolher'
            },
            multiple: false
        });
        custom_uploader.on( 'select', function() {
            attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
            $j( '#' + input_id ).val( attachment.url );
        });
        custom_uploader.open();
    });
}