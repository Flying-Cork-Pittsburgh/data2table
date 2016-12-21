(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Javascript functions and Ajax Request Handling
     */


    // Content Tabs
    //$('ul.tabs li').click(function () {
    $(document).on('click', 'ul.tabs li', function () {
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
    });

    // show field for setting default value
    $(document).on('change', '.field_default_type', function () {
        var value_field = $('#field_default_value_'+ $(this).data('column'));
        if($(this).val() == 'USER_DEFINED'){
            value_field.show();
        }else {
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
        var last_column =  $("#columns").find('tr:last-child');
        var new_column = last_column.clone();
        new_column.replaceWith();
        new_column.find('input').val('');
        new_column.find("input[type='checkbox']").prop('checked', false);;
        new_column.find('select option:first').select();
        last_column.after(new_column);
    });


    //Ajax Request handling

    // submitting sql statement
    $(document).on('click', '.submit-sql-statement', function (event) {
        var sql_target = $(this).attr('id');
        var text_field = $('#sql_statement');
        if(sql_target == "submit-from-creator"){
            text_field = $('#sql_from_creator');
        }
        event.preventDefault();
        var submit_button = $('#'+sql_target);
        $.ajax({
            url: ajaxurl,  // this is part of the JS object you pass in from wp_localize_scripts.
            type: 'post',        // 'get' or 'post', override for form's 'method' attribute
            dataType: 'json',
            data: {
                action: 'ajax_create_table',
                sql:text_field.val()
            },
            beforeSend: function () {
                submit_button.val('Loading data...');
               // submit_button.disable();
            },
            // use beforeSubmit to add your nonce to the form data before submitting.
            beforeSubmit: function (arr, $form, options) {
                arr.push({"name": "nonce", "value": d2t_run_sql_statement.nonce});
            },
            success: function (result) {
                submit_button.val(result);
               // submit_button.enable();
                //TODO render success message
            },
            error: function () {
                submit_button.val('SQL Error');
            }
        });

    })


})(jQuery);

