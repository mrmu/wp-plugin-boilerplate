<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       [AUTHOR_URI]
 * @since      1.0.0
 *
 * @package    Plugin_Slug
 * @subpackage Plugin_Slug/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Plugin_Slug
 * @subpackage Plugin_Slug/public
 * @author     [AUTHOR_NAME] <[AUTHOR_EMAIL]>
 */
class Plugin_Slug_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $general_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->general_settings = Plugin_Slug_Settings_Factory::get_instance('general');
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in plugin-slug_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The plugin-slug_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/plugin-slug-public.css', 
			array(), 
			filemtime( (dirname( __FILE__ )) . '/css/plugin-slug-public.css' ),
			'all' 
		);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in plugin-slug_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The plugin-slug_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/plugin-slug-public.js', 
			array( 'jquery' ), 
			filemtime( (dirname( __FILE__ )) . '/js/plugin-slug-public.js' ),
			false 
		);
		wp_localize_script(
			$this->plugin_name,
			'plugin_slug_public', 
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			)
		);
	}

	public function add_grecaptcha_api_js() {
		?>
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<?php
	}

	private function recaptcha_v2_validation($recap_response){
		$recap_secret = $this->general_settings->get_fd_option('g_recaptcha_v2_sec');
		$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recap_secret.'&response='.$recap_response);
		$response_data = json_decode($response, true);
		if ($response_data["success"]) {
			return true;
		}
		return false;
	}

	// ajax functions
	public function send_to_backend() {
		if (empty($_POST['arg'])) {
			$error = new WP_Error( 'arg_empty', 'Error: No data.', $this->plugin_name );
 			wp_send_json_error( $error );
		}

		if (empty($_POST['recap_response'])) {
			$error = new WP_Error( 'arg_empty', 'Error: No reCaptcha response.', $this->plugin_name );
 			wp_send_json_error( $error );
		}

		$recap_response = sanitize_text_field($_POST['recap_response']);
		$arg = sanitize_text_field($_POST['arg']);

		if (false === $this->recaptcha_v2_validation($recap_response)) {
			$error = new WP_Error( 'recap_fail', 'Error: reCaptcha is invalid, please try again.', $this->plugin_name );
 			wp_send_json_error( $error );
		}
		$return = array(
			'code' => 200,
			'message' => __( 'Success.', $this->plugin_name )
		);
		wp_send_json_success( $return );
	}

	public function register_shortcodes() {
		add_shortcode( 'plugin_slug_form', array($this, 'plugin_slug_form_display') );
	}

	public function plugin_slug_form_display( $atts, $content = '' ) {
		if (is_admin()) {
			return;
		}

		$atts = shortcode_atts( array(
			'mode' => ''
		), $atts, 'plugin_slug_form' );

		$mode = $atts['mode'];
		$g_recap_key = $this->general_settings->get_fd_option('g_recaptcha_v2_key');

		ob_start();
		// whould be $_POST['g-recaptcha-response'] 
		?>
		<div class="g-recaptcha" data-sitekey="<?php echo $g_recap_key;?>"></div>
		<button type="button" id="btn_send_to_backend" class="btn btn-primary">Send to backend</button>
		<?php
		
		// 載入局部自訂的 template 檔的方法：
		// 先從佈景目錄下載入 template 檔 (my-template.php)，
		// 若佈景下的 template 不存在則載入 plugin/templates 下的檔案

		// 若要從此處傳遞變數 $vars 到 template 檔案內
		// set_query_var('vars', $this->vars );

		// if ( $overridden_template = locate_template( 'my-template.php' ) ) {
		// 	load_template( $overridden_template );
		// } else {
		// 	load_template( dirname(dirname( __FILE__ )) . '/templates/my-template.php' );
		// }
		$results = ob_get_clean();
		return $results;
	}
}
