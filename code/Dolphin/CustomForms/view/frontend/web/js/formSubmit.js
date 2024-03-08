require([
    'jquery',
    'mage/url',
    'jquery/ui',
    'mage/validation'
], function ($, urlBuilder) {
    $(document).ready(function() {
        $('.dolphin-custom-form').submit(function(e) {
            if ($(this).validation() && $(this).validation('isValid')) {
                e.preventDefault();

                var formData = new FormData();
                // console.log($(this).serialize());
                $(this).find(':input').each(function() {
                    var field = $(this);
                    var fieldName = field.attr('name');
                    var fieldValue = field.val();

                    if (field.is(':checkbox')) {
                        if (field.is(':checked')) {
                            formData.append(fieldName, fieldValue);
                        }
                    } else if (field.is(':radio')) {
                        if (field.is(':checked')) {
                            formData.append(fieldName, fieldValue);
                        }
                    } else {
                        if (fieldValue !== '' && fieldValue !== undefined) {
                            if (field.is(':file')) {
                                // Handle file input separately
                                var files = field[0].files;
                                for (var i = 0; i < files.length; i++) {
                                    formData.append(fieldName, files[i]);
                                }
                            } else {
                                formData.append(fieldName, fieldValue);
                            }
                        }
                    }
                });

                var baseUrl = urlBuilder.build('dolphinformsdata/index/save');

                $.ajax({
                    type: 'POST',
                    url: baseUrl,
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    showLoader: true,
                    success: function(response) {
                        if (response.success) {
                            console.log(response.message);
                            $("#fieldset").before('<div class="messages"><div class="message message-success success"><div>'+response.message+'</div></div></div>');
                            $('.dolphin-custom-form').trigger('reset');
                        } else {
                            console.log(response.message);
                            $("#fieldset").before('<div class="messages"><div class="message message-error error"><div>'+response.message+'</div></div></div>');
                        }
                    },
                    error: function() {
                        $('#form-messages').html('<p class="error-message">An error occurred while processing your request.</p>');
                    }
                });
            }
        });
    });
});
