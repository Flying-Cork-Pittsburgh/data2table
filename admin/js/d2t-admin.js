(function ($) {
    'use strict';

    /*
     * Javascript functions
     */
    $(document).on('click', 'ul.tabs li', function () {
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');
        $('.tab-content').children('.has-danger').each(function () {
            $(this).removeClass('has-danger').children('.has-danger').each(function () {
                $(this).removeClass('form-control-danger');
            });
        });

        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
    });

    $(document).on('blur', '.required-input', function () {
        var current_input  = $(this);
        if(current_input.val().length == 0){
            current_input.addClass('form-control-danger').parent().addClass('has-danger');
        }else{
            current_input.removeClass('form-control-danger').parent().removeClass('has-danger');
        }
    });

    // delete last column on button click
    $(document).on('click', '.delete-column', function () {
        $('#columns').find('tr').last().remove();
    });

    $(document).on('click', '.add-column', function () {
        var last_column = $("#columns").find('tr:last-child');
        var new_column = last_column.clone();
        new_column.replaceWith();
        new_column.find('input').val('');
        new_column.find("input[type='checkbox']").prop('checked', false);
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
        var create_sql = $('#create-sql-from-creator');
        if (sql_target == "submit-from-creator") {
            text_field = $('#sql_from_creator');
        }
        var submit_button = $('#' + sql_target);
        var submit_button_val = submit_button.val();
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
                    submit_button.val(submit_button_val);
                    submit_button.prop("disabled", false)
                } else {
                    $('.alert-danger').find('.message').text(text);
                    $('.alert-danger').fadeIn("slow");

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
                $('.alert-danger').find('.message').text(errorThrown);
                $('.alert-danger').fadeIn("slow");
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
                    $('.alert-danger').find('.message').text(text);
                    $('.alert-danger').fadeIn("slow");
                }
                submit_button.val('Create SQL');
                submit_button.prop("disabled", false);
                $('.clear-input').prop("disabled", false)

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('.alert-danger').find('.message').text(errorThrown);
                $('.alert-danger').fadeIn("slow");
            }
        });
    });

})(jQuery);

