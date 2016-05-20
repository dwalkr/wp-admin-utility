(function ($) {
    var file_frame;
    var $currentAttachmentField;

    $(document).ready(function () {
        $('#poststuff').on('click', '.ptconfig-attachment-upload', function (e) {
            e.preventDefault();
            $currentAttachmentField = $(this);

            if (file_frame) {
                file_frame.open();
                return;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Add Media',
                button: {
                    text: 'Select'
                },
                multiple: false
            });

            file_frame.on('select', function () {
                var attachment = file_frame.state().get('selection').first().toJSON();
                processFileSelection(attachment);
            });

            file_frame.open();
        });
        $('#poststuff').on('click', '.ptconfig-attachment-remove', function (e) {
            e.preventDefault();
            var $fieldContainer = $(this).parent('.ptconfig-form-field');
            $fieldContainer.find('input').val('');
            $fieldContainer.find('img').hide().attr('src', '');
            $fieldContainer.find('.filename').text('').attr('href','').hide();
            $(this).hide();
        });
    });
    function processFileSelection(attachment) {
        console.log(attachment);
        var $fieldContainer = $currentAttachmentField.parent('.ptconfig-form-field');
        $fieldContainer.find('input:first').val(attachment.url);
        $fieldContainer.find('input[name$="_id"]').val(attachment.id);
        if (attachment.type === 'image') {
            $fieldContainer.find('img').attr('src', attachment.url).show();
        }
        $fieldContainer.find('.filename').text(attachment.filename).attr('href',attachment.url).show();
        $fieldContainer.find('.ptconfig-attachment-remove').show();
    }
})(jQuery);
