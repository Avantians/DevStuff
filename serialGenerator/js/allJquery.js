$( document ).ready( function(){
    // preventing click activate with #
    $('a[href^="#"]').click(function(et) {
        et.preventDefault();
        return false;
    });

    // Initialization
    var formID = $('#setSerialForm');
    var messageId = $('#msg');
    // Form validation (Jquery Validation)
    formID.validate({
        onkeyup: function(element) {
            $(element).valid();
        },
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            required: "Please enter your email adress",
            email: "Please enter a valid email address"
        },
        errorElement: "em",
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass( "help-block" );

            if (element.prop( "type" ) === "checkbox") {
                error.insertAfter( element.parent( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
        submitHandler: function(form) {
            var params = $(form).serialize();
            $.ajax ({
                type: $(form).attr("method"),
                dataType: 'html',
                url: $(form).attr("action"),
                data: params,
                success: function(response) {
                    messageId.html(response); // Set the message text.
                    formID.trigger("reset");  // reset form
                },
                error: function(data) {
                    console.log();
                    // Set the message text.
                    if (data.responseText !== '') {
                        messageId.text(data.responseText);
                    } else {
                        messageId.text('An error occured');
                    }
                }
            });
            return false;
        }
    });
});
