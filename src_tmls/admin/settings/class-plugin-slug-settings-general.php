<?php
class Plugin_Slug_Settings_General extends Plugin_Slug_Settings_Abstract{
	private static $instance;
	private $setting;

	// singleton
    public static function get_instance() {
        if( self::$instance === null ){
            self::$instance = new self();
        }
        return self::$instance;
	}

	public function get_setting() {
		return $this->setting;
	}

	public function __construct() {
		self::$instance = null;

		// 檢查 login page, profile page 是否已建立，若無則於控制項上顯示提示
		$opts = get_option('plugin_slug_general_settings');

		$this->setting = array(
			'tab' => array( 
				'name' => 'general-options', //$_GET arg
				'title' => __('General', 'plugin-slug')
			),
			'option_key' => 'plugin_slug_general_settings',
			'page' => 'plugin-slug-general-section',
			'submit_show' => true,
			'submit_text' => '',
            'sections' => array(
                array(
					'id' => 'plugin-slug-general-main',
					'title' => __('General', 'plugin-slug'),
					'callback' => '',
					'fields' => array(
						'cur_option_key' => array(
							'title' => '',
							'val' => 'plugin_slug_general_settings',
							'callback' => array($this, 'display_hidden_fd'),
						),
						'debug_mode' => array(
							'title' => 'Debug mode',
							'desc' => 'Logger will work if enabled. <a href="'.get_admin_url('', 'admin.php?page=plugin-slug-logger').'">Check log?</a>',
							'placeholder' => '',
							'callback' => array($this, 'display_checkbox_fd'),
						),
						'lic_key' => array(
							'title' => 'License Key',
							'desc' => '',
							'placeholder' => '',
							'callback' => array($this, 'display_text_fd'), //args: fd_id
						),
						'g_recaptcha_v2_key' => array(
							'title' => 'reCaptcha v2 Key',
							'desc' => 'Google reCAPTCHA v2 Key',
							'placeholder' => '',
							'callback' => array($this, 'display_text_fd'), //args: fd_id
						),
						'g_recaptcha_v2_sec' => array(
							'title' => 'reCaptcha v2 Secret',
							'desc' => 'Google reCAPTCHA v2 Secret',
							'placeholder' => '',
							'callback' => array($this, 'display_text_fd'), //args: fd_id
						),
					)
				)
			)
		);
	}

}