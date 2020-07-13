(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).on('click', '.clear_log', function( e ){
		const the_btn = $(this);
		the_btn.prop('disabled', true);
		e.preventDefault();
		$.ajax({
			async: true,
			type: 'POST',
			url: [plugin_slug_funcname]_admin.ajax_url,
			data: {
				action: 'clear_log'
			},
			dataType: 'json',
			success: function(res) {
				// console.log(res);
				if (res.data.done == true) {
					$( '.logger_table tr.data' ).remove();
				}
				the_btn.prop('disabled', false);
			},
			error:function (xhr, ajaxOptions, thrownError){
				alert(ajaxOptions+':'+thrownError);
				the_btn.prop('disabled', false);
			}
		});
	});

})( jQuery );
