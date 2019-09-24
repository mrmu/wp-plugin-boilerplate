<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Wppb
 * @subpackage Wppb/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wppb
 * @subpackage Wppb/public
 * @author     Audi Lu <mrmu@mrmu.com.tw>
 */
class Wppb_Public {

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
		 * defined in wppb_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wppb_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/wppb-public.css', 
			array(), 
			filemtime( (dirname( __FILE__ )) . '/css/wppb-public.css' ),
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
		 * defined in wppb_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wppb_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/wppb-public.js', 
			array( 'jquery' ), 
			filemtime( (dirname( __FILE__ )) . '/js/wppb-public.js' ),
			false 
		);

	}

	public function register_shortcodes() {
		add_shortcode( 'wppb_form', array($this, 'wppb_form_display') );
	}

	public function wppb_form_display() {
		if (!is_admin()) {
			ob_start();
			if ( $overridden_template = locate_template( 'wppb-form.php' ) ) {
				load_template( $overridden_template );
			} else {
				load_template( dirname(dirname( __FILE__ )) . '/templates/wppb-form.php' );
			}
			$results = ob_get_clean();
			return $results;
		}
	}

	public function doer_init() {
		
		// Create ZIP file & Download
		if (isset($_POST['create'])) {
			if ( ! isset( $_POST['wppb_nonce_name'] ) || ! wp_verify_nonce( $_POST['wppb_nonce_name'], 'wppb_nonce_action' ) ) {
			   print 'Sorry, your nonce did not verify.';
			   exit;
			} else {
			   // process form data
			   $args['plugin_slug'] = sanitize_file_name($_POST['plugin_slug']);
			   $args['plugin_name'] = sanitize_text_field($_POST['plugin_name']);
			   $args['plugin_uri'] = esc_url_raw($_POST['plugin_uri']);
			   $args['author_name'] = sanitize_text_field($_POST['au_name']);
			   $args['author_email'] = sanitize_email($_POST['au_email']);
			   $args['author_uri'] = esc_url_raw($_POST['au_uri']);
   
			   $wppb_doer = Wppb_Doer::get_doer($args);
			   $zipfile = $wppb_doer->run();
			   if (file_exists($zipfile)) {
				   header("Pragma: public");
				   header("Expires: 0");
				   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				   header("Cache-Control: public");
				   header("Content-Description: File Transfer");
				   header("Content-type: application/octet-stream");
				   header("Content-Transfer-Encoding: binary");
				   header('Content-Disposition: attachment; filename="'.basename($zipfile).'"');
				   header('Content-Length: ' . filesize($zipfile));
				   flush();
				   readfile($zipfile);
				   // delete file
				   unlink($zipfile);
				   exit();
			   }
			}
		}
	}

}
