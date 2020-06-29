<?php
class [plugin_slug_classname]_Settings {
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

	private $page;
	private $valid_tab_names;
	private $all_tabs;
	private $settings;
	private $logger_name;

	public function __construct( $plugin_name, $version ) {
		$this->page = $plugin_name;
		$this->logger_name = '[plugin_slug]-logger'; // also be option key
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$partial_settings_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/');
		include_once $partial_settings_dir . 'class-[plugin_slug]-settings-factory.php';
		include_once $partial_settings_dir . 'class-[plugin_slug]-settings-general.php';
		// include_once $partial_settings_dir . 'class-[plugin_slug]-settings-others.php';

		$this->settings = array(
			'General' => [plugin_slug_classname]_Settings_Factory::get_instance('general'),
			// 'Others' => [plugin_slug_classname]_Settings_Factory::get_instance('others'),
		);
	}

	// for settings page //

	private function get_active_tab() {
		$active_tab = $this->valid_tab_names[0]; // default tab
		if (isset($_GET['tab']) && (in_array($_GET['tab'], $this->valid_tab_names))) {
			$active_tab = $_GET['tab'];
		}
		return $active_tab;
	}

	// for log page //

	public function clear_log(){
		update_option($this->logger_name, array(), false);
		$return = array(
			'done' => true
		);
		wp_send_json_success( $return );
	}

	public function add_log($func_name, $data = ''){
		$log = get_option($this->logger_name);
		if(empty($log)){
			$log = array();
		}

		$log[] = array(
			'timestamp' => date("Y-m-d H:i:s"),
			'func_name' => $func_name,
			'data' => $data,
		);

		$log = array_slice($log, -99, 99);
		update_option($this->logger_name, $log, false);
	}

	/**
	 * Register sections fields and settings
	 */
	public function register() {

		foreach ($this->settings as $key => $obj) {
			$obj_setting = $obj->get_setting();
			$this->all_tabs[] = $obj_setting['tab'];
			$this->valid_tab_names[] = $obj_setting['tab']['name'];
		}

		$active_tab = $this->get_active_tab();
		foreach ($this->settings as $key => $obj) {
			$obj_setting = $obj->get_setting();
			$sec_id = $obj_setting['section']['id'];
			$sec_title = $obj_setting['section']['title'];
			$sec_callback = $obj_setting['section']['callback'];
			$sec_page = $obj_setting['section']['page'];
			if ($obj_setting['tab']['name'] === $active_tab) {
				add_settings_section(
					$sec_id,			// ID of the settings section
					$sec_title,  		// Title of the section
					$sec_callback,		// Callback
					$sec_page			// ID of the page
				);
				$fds = $obj_setting['fields'];
				foreach ($fds as $fd_id => $fd_setting) {
					add_settings_field(
						$fd_id, 					// The ID of the settings field
						$fd_setting['title'], 		// The name of the field of setting(s)
						$fd_setting['callback'],
						$sec_page, 					// ID of the page on which to display these fields
						$sec_id, 					// The ID of the setting section
						$fd_id	 					// callback args
					);
					// save to custom option
					if (!empty($fd_setting['option_key'])){
						register_setting(
							$sec_page, 					// Group of options
							$fd_setting['option_key'], 	// Name of options
							array( $this, 'sanitize' )	// Sanitization function
						);
					}
				}
			}
			register_setting(
				$sec_page, 						// Group of options
				$obj_setting['option_key'], 	// Name of options
				array( $this, 'sanitize' )		// Sanitization function
			);
		}
	}

	/**
	 * Simple sanitize function
	 * @param $input
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = '';
		do_action('[plugin_slug_funcname]_log', 'sanitize input: ', $input);

		// Loop through the input and sanitize each of the values
		if (is_array($input)) {
			$new_input = array();
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = sanitize_text_field( $val );
			}
		}else{
			$new_input = $input;
		}
		do_action('[plugin_slug_funcname]_log', 'sanitize output: ', $new_input);

		return $new_input;
	}

	public function add_menu_items() {
		add_menu_page(
			'[PLUGIN_NAME]', 
			__('[PLUGIN_NAME]', 
			$this->plugin_name), 
			'manage_options', 
			$this->page
		);
		add_submenu_page(
			$this->page, // 設定/ options-general.php
			__('Settings', $this->plugin_name),
			__('Settings', $this->plugin_name),
			'manage_options',
			$this->page,
			array( $this, 'display_settings_page' )
		);
		add_submenu_page(
			$this->page, // 設定/ options-general.php
			__('Log', $this->plugin_name),
			__('Log', $this->plugin_name),
			'manage_options',
			$this->logger_name,
			array( $this, 'display_log_page' )
		);
	}
	public function display_log_page() {

		echo '<h2>'.__('Log', $this->plugin_name).'</h2>';
		echo '<div class="wrap">';
		echo '<p>For adding data to log use the hook: <code>do_action( \'[plugin_slug_funcname]_log\', $data );</code></p>';
		echo '<a class="button clear_log" href="">'.__('Clear Log', $this->plugin_name).'</a><hr>';

		$data = get_option($this->logger_name);
		if( ! is_array($data)){
			echo '<p>These is no data in the log.</p>';
			return;
		}

		$data = array_reverse($data);
		?>
		<style>.dblClickToScroll{ cursor : pointer } </style>
		<table class="logger_table wp-list-table widefat fixed striped">
			<thead>
			<tr>
				<th class="manage-column column-cb" scope="col" style="width:10%;">Date</th>
				<th class="manage-column column-cb" scope="col" style="width:10%;">Func</th>
				<th class="manage-column column-cb" scope="col" style="width:75%;">Data</th>
			</tr>
			</thead>
			<tbody>
			<?php 
			$i = 0;
			foreach($data as $item): 
				$i++;
				?>
				<tr class="data" id="loggerDataRow_<?php echo $i; ?>">
					<td class="dblClickToScroll column-columnname" title="Double click for scrolling to next row">
						<span><?php echo $item['timestamp']; ?></span>
					</td>
					<td class="column-columnname">
						<?php echo $item['func_name']; ?>
					</td>
					<td class="column-columnname">
						<pre><?php print_r($item['data']) ?></pre>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		echo '</div>';// end of wrap
	}

	public function display_settings_page() {
		$active_tab = $this->get_active_tab();
		?>
		<div class="wrap">
			<h2>Settings</h2>
			<!-- wordpress provides the styling for tabs. -->
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ($this->all_tabs as $tab) {
					$active_class = '';
					if ($active_tab === $tab['name']) {
						$active_class = 'nav-tab-active';
					}
					// render Tabs
					echo '<a href="?page='.$this->page.'&tab='.$tab['name'].'" class="nav-tab '.$active_class.'">'.$tab['title'].'</a>';
				} 
				?>
			</h2>
			<form method="post" enctype="multipart/form-data" action="options.php">
				<?php
				// render controls of form
				foreach ($this->settings as $key => $obj) {
					$obj_setting = $obj->get_setting();
					$tab_name = $obj_setting['tab']['name'];
					$sec_page = $obj_setting['section']['page'];					
					if ($active_tab === $tab_name) {
						settings_fields( $sec_page );
						do_settings_sections( $sec_page );
					}
				}
				submit_button();
				?>
			</form>
		</div><!-- .wrap -->
	<?php
	}
}