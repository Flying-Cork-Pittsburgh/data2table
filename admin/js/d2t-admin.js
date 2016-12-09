(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Javascript functions and Ajax Request Handling
	 */

//Ajax Request handling
	$( document ).on( 'click', '#submit-sql-statement', function(event) {
		event.preventDefault();
		var submit_button = $('#submit-sql-statement');
		$.ajax({
			url: ajaxurl,  // this is part of the JS object you pass in from wp_localize_scripts.
			type: 'post',        // 'get' or 'post', override for form's 'method' attribute
			dataType: 'json',
			data : {
				action : 'ajax_create_table',
				sql : $( "#sql_statement").val()
			},
			beforeSend: function() {
				submit_button.val( 'Loading data...' );
			},
			// use beforeSubmit to add your nonce to the form data before submitting.
			beforeSubmit: function (arr, $form, options) {
				arr.push({"name": "nonce", "value": d2t_run_sql_statement.nonce});
			},
			success: function (result) {
                //TODO write result better than into button
				submit_button.val( result );
                //TODO render success message
			},
			error:function () {
				submit_button.val( 'SQL Error' );
			}
		});

	})
})( jQuery );

