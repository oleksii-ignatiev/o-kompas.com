/*
 * @title Main App
 * @description Application entry point
 */


// import { stickyHeader } from 'header/sticky-header';
// import { entryMyself } from './components/entry_myself';


document.addEventListener('DOMContentLoaded', () => {

    jQuery(".form-entry-myself").submit(function (e) {
        e.preventDefault();
        var form = jQuery(this);
        console.log(form);

        jQuery.ajax({
            url: ocm.ajax_url,
            data: {
                action: 'ocm_entry_myself',
                formData: form.serialize(),
            },
            method: 'POST',
            beforeSend: function (xhr) {
                // Clear Errors
                form.find('.form-output').html('');
            },
            success: function (data) {
                console.log(data);
                if (data) {
                    if (data.success) {
                        // handle success, show message
                        var html = "";
                        form.find('.form-output').append('<p class="success">' + ocm.success_message + '</p>');
                        
                        for (const [key, value] of Object.entries(data.entry)) {
                            
                            html += '<td>' + value + '</td>';
                           
                        };
                        
                        jQuery(".entry-table tr:last").after("<tr>" + html + "</tr>");
                        deleteEntry();
                    }

                    if (data.has_errors && data.errors.length > 0) {
                        var err = '';
                        data.errors.forEach(function (error) {
                            err += '<li>' + error + '</li>'
                        });

                        form.find('.form-output').append('<ul>' + err + '</ul>');
                    }
                }
            },

            error: function (err) {
                console.log(' ОШИБКИ ');
                console.log(err);
            }
        });
    });
    
    function deleteEntry() {
        jQuery(".delete-entry").on('click', function (e) {
            e.preventDefault();
            const userID = jQuery(this).parents('tr').find('td:nth-child(2)').html(),
                form = jQuery('.form-entry-myself'),
                eventID = jQuery(this).data('event-id'),
                row = jQuery(this).parents('tr');
            
            jQuery.ajax({
                url: ocm.ajax_url,
                data: {
                    action: 'ocm_delete_entry_myself',
                    userID,
                    eventID
                },
                method: 'POST',
                beforeSend: function (xhr) {
                    // Clear Errors
                    form.find('.form-output').html('');
                },
                success: function (data) {
                    if (data) {
                        if (data.success) {
                            // handle success, show message
                            
                            form.find('.form-output').append('<p class="success">' + data.success_message + '</p>');
                            row.hide(500);
                        }

                        if (data.has_errors && data.errors.length > 0) {
                            var err = '';
                            data.errors.forEach(function (error) {
                                err += '<li>' + error + '</li>' // build the list
                            });

                            form.find('.form-output').append('<ul>' + err + '</ul>');
                        }
                    }
                },

                error: function (err) {
                    console.log(' ОШИБКИ ');
                    console.log(err);
                }
            });
        });
    };
    deleteEntry();

    function editEntry() {
        jQuery(".edit-entry").on('click', function (e) {
            e.preventDefault();
            const userID = jQuery(this).parents('tr').find('td:nth-child(2)').html(),
                form = jQuery('.form-entry-myself'),
                eventID = jQuery(this).data('event-id'),
                group = jQuery(this).data('group-number'),
                selectedGroup = jQuery(this).parents('tr').find(`td:nth-child(${group})`).html(),
                row = jQuery(this).parents('tr');

            jQuery.ajax({
                url: ocm.ajax_url,
                data: {
                    action: 'ocm_edit_entry_myself',
                    userID,
                    group,
                    selectedGroup,
                    eventID
                },
                method: 'POST',
                beforeSend: function (xhr) {
                    // Clear Errors
                    form.find('.form-output').html('');
                },
                success: function (data) {
                    if (data) {
                        if (data.success) {
                            // handle success, show message

                            form.find('.form-output').append('<p class="success">' + data.success_message + '</p>');
                            row.hide(500);
                        }

                        if (data.has_errors && data.errors.length > 0) {
                            var err = '';
                            data.errors.forEach(function (error) {
                                err += '<li>' + error + '</li>' // build the list
                            });

                            form.find('.form-output').append('<ul>' + err + '</ul>');
                        }
                    }
                },

                error: function (err) {
                    console.log(' ОШИБКИ ');
                    console.log(err);
                }
            });
        });
    };
    editEntry();
});