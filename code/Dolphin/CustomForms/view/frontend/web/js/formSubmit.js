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

                var formData = $(this).serialize();
                var baseUrl = urlBuilder.build('/dolphinformsdata/index/save');

                $.ajax({
                    type: 'POST',
                    url: baseUrl,
                    data: formData,
                    dataType: 'json',
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
