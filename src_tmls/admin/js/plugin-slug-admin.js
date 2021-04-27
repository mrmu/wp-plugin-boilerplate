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

	$(function(){
		$('.hide-tr').each(function(){
			$(this).closest('tr').hide();
		});
		$('.hide-th').each(function(){
			$(this).closest('tr').find('th').html('');
		});
		// upload file
		$('body').on('change', '.custom-file-input', function(){
			let fileName = $(this)[0].files[0].name;
			$(this).next('.custom-file-label').addClass("selected").html(fileName);
		});

		$(document).on('click', '.clear_log_plugin_slug', function( e ){
			const the_btn = $(this);
			the_btn.prop('disabled', true);
			e.preventDefault();
			$.ajax({
				async: true,
				type: 'POST',
				url: plugin_slug_admin.ajax_url,
				data: {
					action: 'clear_log_plugin_slug'
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

	});

})( jQuery );
