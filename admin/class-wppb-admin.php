<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Wppb
 * @subpackage Wppb/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wppb
 * @subpackage Wppb/admin
 * @author     Audi Lu <mrmu@mrmu.com.tw>
 */
class Wppb_Admin {

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

	private $class_deps_check;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// $this->class_deps_check = array(
		// 	'woocommerce' => array(
		// 		'status' => false,
		// 		'err_msg' => __('WooCommerce is not activated, please activate it to use plugin: WPPB.', $this->plugin_name)
		// 	),
		// );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/wppb-admin.css', 
			array(), 
			filemtime( (dirname( __FILE__ )) . '/css/wppb-admin.css' ), 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/wppb-admin.js', 
			array( 'jquery' ), 
			filemtime( (dirname( __FILE__ )) . '/js/wppb-admin.js' ), 
			false 
		);

	}

	public function class_deps_check_active() {
		if (!is_array($this->class_deps_check)) {
			return;
		}
		foreach ($this->class_deps_check as $chk_class => $info) {
			if ( class_exists( $chk_class ) ) {
				$this->class_deps_check[$chk_class]['status'] = true;
			} else {
				$this->class_deps_check[$chk_class]['status'] = false;
			}
		}
	}

	public function class_deps_check_admin_notice() {
		if (is_array($this->class_deps_check)) {
			foreach ($this->class_deps_check as $chk_class => $info) {
				if ( $this->class_deps_check[$chk_class]['status'] === false ){
					?>
					<div class="notice notice-error is-dismissible">
						<p>
							<?php echo $this->class_deps_check[$chk_class]['err_msg']; ?>
						</p>
					</div>
					<?php
				}
			}
		}

		$composer_json_path = PLUGIN_SLUG_PATH . 'composer.json';
		if (is_file($composer_json_path)) {
			$composer_autoload_path = PLUGIN_SLUG_PATH . 'vendor/autoload.php';
			if (!is_file($composer_autoload_path)) {
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<?php printf(__('Please install Composer dependencies. (%s)', $this->plugin_name), $this->plugin_name); ?>
					</p>
				</div>
				<?php
			}
		}
	}

}
