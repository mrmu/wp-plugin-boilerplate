(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	
    let do_send_to_backend = function(data, the_btn) {
		console.log('do_send_to_backend');
		let ori_btn_title = the_btn.html();
		the_btn.html('...');

        // console.log(data);
		$.ajax({
			async: true,
			type: 'POST',
			url: [plugin_slug_funcname]_public.ajax_url,
			data: data,
			dataType: 'json',
			success: function(res) {
				console.log(res);
				if (res.success === false) {
					alert(res.data[0].message);
				}else{
					alert(res.data.message);
				}
				the_btn.html(ori_btn_title).prop('disabled', false);
			},
			error:function (xhr, ajaxOptions, thrownError){
				console.log(ajaxOptions+':'+thrownError);
				the_btn.html(ori_btn_title).prop('disabled', false);
			}
		});
	};
	
	$(function(){

		$(document).on('click', '#btn_send_to_backend', function(e){
			const the_btn = $(this);
			const data = {
				action: 'send_to_backend',
				arg: 'arg1',
				recap_response: grecaptcha.getResponse()
			};
			the_btn.prop('disabled', true);
			do_send_to_backend(data, the_btn);
		});

	});

})( jQuery );
