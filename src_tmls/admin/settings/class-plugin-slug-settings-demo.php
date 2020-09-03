<?php
class Plugin_Slug_Settings_Demo extends Plugin_Slug_Settings_Abstract{
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
		$opts = get_option('plugin_slug_demo_settings');

		$this->setting = array(
			'tab' => array( 
				'name' => 'demo-options', //$_GET arg
				'title' => __('Demo', 'plugin-slug')
			),
			'option_key' => 'plugin_slug_demo_settings',
			'page' => 'plugin-slug-demo-section',
			'submit_show' => true,
			'submit_text' => '',
            'sections' => array(
                array(
					'id' => 'plugin-slug-demo-main',
					'title' => __('Demo', 'plugin-slug'),
					'callback' => '',
					'fields' => array(
						'cur_option_key' => array(
							'title' => '',
							'val' => 'plugin_slug_demo_settings',
							'callback' => array($this, 'display_hidden_fd'),
						),
						'demo_checkbox' => array(
							'title' => 'Demo check',
							'desc' => 'demo check',
							'placeholder' => '',
							'callback' => array($this, 'display_checkbox_fd'),
						),
						'demo_text' => array(
							'title' => 'Demo text',
							'desc' => '',
							'placeholder' => '',
							'callback' => array($this, 'display_text_fd'), //args: fd_id
						),
                        'demo_date' => array(
                        	'title' => 'Demo date',
                        	'desc' => '',
                        	'placeholder' => '',
                        	'callback' => array($this, 'display_date_fd'), //args: fd_id
						),
                        'demo_date' => array(
                        	'title' => 'Demo date',
                        	'desc' => 'demo date',
                        	'placeholder' => '',
                        	'callback' => array($this, 'display_date_fd'), //args: fd_id
						),
						'demo_select' => array(
                        	'title' => 'Demo select',
                        	'desc' => 'demo dropdown',
                        	'options' => ['key1' => 'val1', 'key2' => 'val2'],
                        	'callback' => array($this, 'display_select_fd'), //args: fd_id
						),
						'demo_btn' => array( 
							'type' => 'button',
							'title' => 'Demo button',
							'val' => 'demo_btn',
							'id' => 'demo_btn',
							'class' => 'button hide-th',
							'callback' => array($this, 'display_btn')
						),
						// 要搭配 submit btn 的 value，在 handle_before_save() 裡
						// 判斷 submit btn 的 value，再處理上傳後續的動作
						'demo_upload' => array( 
							'title' => 'Demo upload file',
							'type' => 'file',
							'desc' => '選擇要上傳的檔案',
							'id' => '',
							'class' => '',
							'accept' => '.xlsx',
							'show_files' => 'no',
							'callback' => array($this, 'display_upload')
						),
						'demo_submit_btn' => array( 
							'type' => 'submit',
							'title' => 'Demo submit upload',
							'val' => 'demo_submit_upload',
							'id' => 'demo_submit_upload',
							'class' => 'button button-primary hide-th',
							'callback' => array($this, 'display_btn')
						)
					)
				)
			)
		);
	}

}