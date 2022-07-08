<?php
class Plugin_Slug_Utils {
    public function __construct() {
	}

	// [Usage]
	// $admin_email = get_bloginfo( 'admin_email' );
	// $email_args = array(
	// 	'to' => $admin_email,
	// 	'subject' => $subject,
	// 	'message_heading' => $subject,
	// 	'message_body' => $message
	// );
	// $mail_rst = Plugin_Slug_Utils::wc_mail( $email_args );
	// if (is_wp_error($mail_rst)) {
	// 	// wp_send_json_error( $mail_rst );
	// }

	// if ($mail_rst === false) {
	// 	$error = new WP_Error( 'wc_mail_fail', 'Error: Please try again later.', $this->plugin_name );
	// }

	public static function wc_mail($args) {
		if (empty($args) || !is_array($args) || empty($args['to']) ) {
			return new WP_Error( 'invalid_args', __( 'WC Mail: Invalid arguments.', 'plugin-slug' ) );
		}
		
		$to = $args['to'];
		$subject = $args['subject'];
		$message_heading = $args['message_heading'];
		$message_body = $args['message_body'];
		$attachment = '';
		if( !empty($args['attachment']) ){
			$attachment = $args['attachment'];	
		}
		$mailer = WC()->mailer();		
		$message = $mailer->wrap_message($message_heading, $message_body );
		return $mailer->send( $to, $subject, $message, '', $attachment);
	}
}