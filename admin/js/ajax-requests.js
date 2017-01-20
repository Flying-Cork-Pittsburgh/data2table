(function ($) {
    'use strict';
    $(document).ready(function () {
        /**
         *Ajax Request Handling
         */

        // submitting sql statement
        $(document).on('click', '.submit-sql-statement', function (event) {
            event.preventDefault();
            var sql_target = $(this).attr('id');
            var text_field = $('#sql_statement');
            var create_sql = $('#create-sql-from-creator');
            if (sql_target == "submit-from-creator") {
                text_field = $('#sql_from_creator');
            }
            var submit_button = $('#' + sql_target);
            var submit_button_val = submit_button.val();
            var alert_danger = $('.alert-danger');
            var alert_success = $('.alert-success');

            $.ajax({
                url: ajaxurl,  // this is part of the JS object you pass in from wp_localize_scripts.
                type: 'post',        // 'get' or 'post', override for form's 'method' attribute
                dataType: 'json',
                data: {
                    action: 'ajax_create_table',
                    sql: text_field.val()
                },
                beforeSend: function () {
                    submit_button.val('Please wait ...');
                    submit_button.prop("disabled", true);
                    $('.alert').fadeOut("slow");
                },
                // use beforeSubmit to add your nonce to the form data before submitting.
                beforeSubmit: function (arr, $form, options) {
                    arr.push({"name": "nonce", "value": d2t_run_sql_statement.nonce});
                },
                success: function (result) {
                    var text = result.data;
                    if (result.success) {
                        alert_success.find('.message').text(text);
                        alert_success.fadeIn("slow");
                        submit_button.val(submit_button_val);
                        submit_button.prop("disabled", false)
                    } else {
                        alert_danger.find('.message').text(text);
                        alert_danger.fadeIn("slow");

                        if (submit_button.selector == '#submit-from-creator') {
                            submit_button.hide();
                            create_sql.show();
                        } else {
                            submit_button.val(submit_button_val);
                        }
                        submit_button.prop("disabled", false);
                    }

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert_danger.find('.message').text(errorThrown);
                    alert_danger.fadeIn("slow");
                }
            });

        });

        // pack values from creator into json to create sql statement from Backend
        $(document).on('click', '#create-sql-from-creator', function (event) {
            event.preventDefault();
            var table = {};
            table['table_name'] = $('#table_name').val();
            var columns = $('#columns').find('.column');
            table['columns'] = [];
            var i = 0;
            columns.each(function () {
                if ($(this).find('.field_name').val().length != 0) {
                    var properties = {};
                    properties['name'] = $(this).find('.field_name').val();
                    properties['type'] = $(this).find('.field_type').val();
                    properties['default'] = $(this).find('.field_default_value').val();
                    if (properties['default'] != '') {
                        properties['default'] = "DEFAULT '" + properties['default'] + "'";
                    }
                    properties['constraint'] = '';
                    if ($(this).find('.field_not_null').attr('checked')) {
                        properties['constraint'] += $(this).find('.field_not_null').val() + ' ';
                    }
                    if ($(this).find('.field_is_unique').attr('checked')) {
                        properties['constraint'] += $(this).find('.field_is_unique').val();
                    }
                    table['columns'][i] = properties;
                    i++;
                }
            });

            var alert_danger = $('.alert-danger');
            var submit_button = $(this);
            $.ajax({
                url: ajaxurl,  // this is part of the JS object you pass in from wp_localize_scripts.
                type: 'post',        // 'get' or 'post', override for form's 'method' attribute
                dataType: 'json',
                data: {
                    action: 'ajax_build_sql_statement',
                    values: table
                },
                beforeSend: function () {
                    submit_button.val('Please wait ...');
                    submit_button.prop("disabled", true);
                    $('.alert').fadeOut("slow");
                },
                // use beforeSubmit to add your nonce to the form data before submitting.
                beforeSubmit: function (arr, $form, options) {
                    arr.push({"name": "nonce", "value": d2t_create_sql_statement.nonce});
                },
                success: function (result) {
                    var text = result.data;
                    text = text.replace(/\\/g, '');
                    if (result.success) {
                        $('#sql_from_creator').text(text);
                        $('#sql_from_creator').fadeIn("slow");
                        submit_button.hide();
                        $('#submit-from-creator').show();
                    } else {
                        alert_danger.find('.message').text(text);
                        alert_danger.fadeIn("slow");
                    }
                    submit_button.val('Create SQL');
                    submit_button.prop("disabled", false)
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert_danger.find('.message').text(errorThrown);
                    alert_danger.fadeIn("slow");
                }
            });
        });

        // upload file
        $(".file-upload-button").click(function (event) {
            event.preventDefault();
            var alert_danger = $('.alert-danger');
            var alert_success = $('.alert-success');
            var submit_button = $(this);
            var submit_button_val = submit_button.val();

            submit_button.prop('disabled', true);
            submit_button.val('please wait...');
            alert_danger.hide();

            var over = '<div id="overlay" style="display: none;"><span id="loading">' +
                '<p>Please wait while loading</p></span></div>';
            $(over).appendTo('body').fadeIn("slow");

            var formData = new FormData();
            formData.append('file', $('#FileInput')[0].files[0]);
            formData.append('table_name', $('#table_name').val());
            formData.append('action', 'ajax_test_import_file');

            $.ajax({
                url: ajaxurl,  // this is part of the JS object you pass in from wp_localize_scripts.
                type: 'post',        // 'get' or 'post', override for form's 'method' attribute
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function () {
                    submit_button.val('Please wait ...');
                    submit_button.prop("disabled", true);
                    alert_danger.fadeOut("slow");
                },
                // use beforeSubmit to add your nonce to the form data before submitting.
                beforeSubmit: function (arr, $form, options) {
                    arr.push({"name": "nonce", "value": d2t_upload_file.nonce});
                },
                success: function (result) {
                    var text = result.data['message'];
                    if (result.success) {
                        var diff = Object.values(result.data['property_difference']);
                        var preview_table = result.data['preview'];
                        var preview = $('#preview');
                        alert_success.find('.message').text(text);
                        alert_success.fadeIn("slow");
                        $(preview).append(preview_table);
                        preview.fadeIn("slow");
                        if(diff.length > 0){
                        var preview_text = preview.find('#preview-alert').text();
                        preview.find('#preview-alert').text( preview_text +
                            'The following properties are not contained in the import file: ' + diff.join(', '));
                        }
                        submit_button.val("Import Data");
                    } else {
                        alert_danger.find('.message').text(text);
                        alert_danger.fadeIn("slow");
                        submit_button.val(submit_button_val);
                    }
                    submit_button.prop("disabled", false);
                    $('#overlay').fadeOut('slow',
                        function (here) {
                            $(here).remove();
                        });
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert_danger.find('.message').text(errorThrown);
                    alert_danger.fadeIn("slow");
                    submit_button.val(submit_button_val);
                    submit_button.prop("disabled", false);
                    $('#overlay').fadeOut('slow',
                        function (here) {
                            $(here).remove();
                        });
                }
            });
        });
    })

})(jQuery);

