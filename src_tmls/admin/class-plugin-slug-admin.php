<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       [AUTHOR_URI]
 * @since      1.0.0
 *
 * @package    Plugin_Slug
 * @subpackage Plugin_Slug/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Slug
 * @subpackage Plugin_Slug/admin
 * @author     [AUTHOR_NAME] <[AUTHOR_EMAIL]>
 */
class Plugin_Slug_Admin {

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
		// 		'err_msg' => __('WooCommerce is not activated, please activate it to use plugin: [PLUGIN_NAME].', $this->plugin_name)
		// 	),
		// );

	}

	private function is_enqueue_pages($hook) {
		global $post, $post_type;

		if (empty($post_type)){
			if (!empty($post)) {
				$post_type = $post->post_type;
			}
		}

		$load_post_types = array(
			//post type slugs ...
		);

		$apply_pages = array(
			'post-new.php' => $load_post_types, 		// new post
			'edit.php' => $load_post_types,				// post list
			'post.php' => $load_post_types,				// post edit
			'toplevel_page_plugin-slug',				// Settings
			'plugin-slug_page_plugin-slug-logger'	// Log
		);

		// echo 'hook:['.$hook.'] '; // ref page name
		// echo 'post_type:['.$post_type.'] ';

		foreach ($apply_pages as $pg => $pts) {
			if ($pg === $hook) {
				if (!empty($pts) && is_array($pts)) {
					if (in_array($post_type, $pts)) {
						return true;
					}
				}
				return true;
			}else if ($pts === $hook) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

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

		if ($this->is_enqueue_pages($hook)) {
			wp_enqueue_style( 
				$this->plugin_name, 
				plugin_dir_url( __FILE__ ) . 'css/plugin-slug-admin.css', 
				array(), 
				filemtime( (dirname( __FILE__ )) . '/css/plugin-slug-admin.css' ), 
				'all' 
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

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

		if ($this->is_enqueue_pages($hook)) {
			wp_enqueue_script( 
				$this->plugin_name, 
				plugin_dir_url( __FILE__ ) . 'js/plugin-slug-admin.js', 
				array( 'jquery' ), 
				filemtime( (dirname( __FILE__ )) . '/js/plugin-slug-admin.js' ), 
				false 
			);
			wp_localize_script(
				$this->plugin_name,
				'plugin_slug_admin', 
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				)
			);
		}
	}

	// check if the plugin need other class (that from another plugin)
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
		if ( is_array($this->class_deps_check) ) {
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
