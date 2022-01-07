export function entryMyself() {

    $(".form-entry-myself").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        console.log(form);

        // $.ajax({
        //     url: ds.ajax_url,
        //     data: {
        //         action: 'ds_reset_password',
        //         formData: form.serialize(),
        //     },
        //     type: 'POST',
        //     beforeSend: function (xhr) {
        //         // Clear Errors
        //         form.find('.form-output').html('');
        //     },
        //     success: function (data) {
        //         console.log(data);
        //         if (data) {
        //             if (data.success) {
        //                 // handle success, show message
        //                 form.find('.form-output').append('<p class="success">' + ds.reset_pass_success_message + '</p>');

        //             }

        //             if (data.has_errors && data.errors.length > 0) {
        //                 var err = '';
        //                 data.errors.forEach(function (error) {
        //                     err += '<li>' + error + '</li>' // build the list
        //                 });

        //                 form.find('.form-output').append('<ul>' + err + '</ul>');
        //             }
        //         }
        //     },

        //     error: function (err) {
        //         console.log(err);
        //     }
        // });
    });
}