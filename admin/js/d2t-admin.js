(function ($) {
    'use strict';

    /*
     * Javascript functions
     */

    $(document).on('click', 'ul.tabs li', function () {
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
    });

    // show field for setting default value
    $(document).on('change', '.field_default_type', function () {
        var value_field = $('#field_default_value_' + $(this).data('column'));
        if ($(this).val() == 'USER_DEFINED') {
            value_field.show();
        } else {
            value_field.hide();
        }
    });

    // delete column on button click
    $(document).on('click', '.delete-column', function () {
        var column_id = $(this).data('column');
        $("tr").find("[data-column='" + column_id + "']").closest('tr').remove();
    });

    // duplicate last column
    //TODO the column_id is not incremented - so the saving will not work
    $(document).on('click', '.add-column', function () {
        var last_column = $("#columns").find('tr:last-child');
        var new_column = last_column.clone();
        new_column.replaceWith();
        new_column.find('input').val('');
        new_column.find("input[type='checkbox']").prop('checked', false);
        ;
        new_column.find('select option:first').select();
        last_column.after(new_column);
    });

    // clear input field on cancel click
    $(document).on('click', '.clear-input', function () {
        event.preventDefault();
        var sql_target = $(this).attr('id');
        var text_field = $('#sql_statement');
        if (sql_target == "clear-sql-from-creator") {
            text_field = $('#sql_from_creator');
        }
        text_field.val('');
    });

    /**
     *Ajax Request Handling
     */

    // submitting sql statement
    $(document).on('click', '.submit-sql-statement', function (event) {
        event.preventDefault();
        var sql_target = $(this).attr('id');
        var text_field = $('#sql_statement');
        if (sql_target == "submit-from-creator") {
            text_field = $('#sql_from_creator');
        }
        var submit_button = $('#' + sql_target);
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
                    $('.alert-success').find('.message').text(text);
                    $('.alert-success').fadeIn("slow");
                    submit_button.val('Run');
                    submit_button.prop("disabled", false)
                } else {
                    $('.alert-danger').find('.message').text(text);
                    $('.alert-danger').fadeIn("slow");
                    submit_button.val('Run');
                    submit_button.prop("disabled", false)
                }

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('.alert-danger').find('.message').text(errorThrown);
                $('.alert-danger').fadeIn("slow");
                submit_button.val('Run');
                submit_button.prop("disabled", false)
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
                // TODO get contraints and CO

                table['columns'][i] = properties;
                i++;
            }
        });

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
                if (result.success) {
                    $('#sql_from_creator').text(text);
                    $('#sql_from_creator').fadeIn("slow");
                    submit_button.val('Create SQL');
                    submit_button.prop("disabled", false)
                } else {
                    $('.alert-danger').find('.message').text(text);
                    $('.alert-danger').fadeIn("slow");
                    submit_button.val('Create SQL');
                    submit_button.prop("disabled", false)
                }

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('.alert-danger').find('.message').text(errorThrown);
                $('.alert-danger').fadeIn("slow");
            }
        });
    });


})(jQuery);

