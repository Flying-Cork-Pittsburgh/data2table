(function ($) {
    'use strict';
    $(document).ready(function () {

        /*
         * Javascript functions
         */
        $("ul.tabs li").click(function () {
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

        $(".required-input").blur(function () {
            var current_input = $(this);
            if (current_input.val().length == 0) {
                current_input.addClass('form-control-danger').parent().addClass('has-danger');
            } else {
                current_input.removeClass('form-control-danger').parent().removeClass('has-danger');
            }
        });

        // delete last column on button click
        $(".delete-column").click(function () {
            $('#columns').find('tr').last().remove();
        });

        // appends new column on button click
        $(".add-column").click(function () {
            var last_column = $("#columns").find('tr:last-child');
            var new_column = last_column.clone();
            new_column.replaceWith();
            new_column.find('input').val('');
            new_column.find("input[type='checkbox']").prop('checked', false);
            new_column.find('select option:first').select();
            last_column.after(new_column);
        });

        // clear input field on cancel click
        $(".clear-input").click(function (event) {
            event.preventDefault();
            var sql_target = $(this).attr('id');
            var text_field = $('#sql_statement');
            if (sql_target == "clear-sql-from-creator") {
                text_field = $('#sql_from_creator');
            }
            text_field.val('');
        });
    })
})(jQuery);

