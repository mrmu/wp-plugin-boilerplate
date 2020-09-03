<?php
class Plugin_Slug_Settings {
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
		$this->plugin_name = $plugin_name;
		$this->logger_name = $plugin_name.'-logger'; // also be option key
		$this->version = $version;
		$settings_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/');
		include_once $settings_dir . 'class-plugin-slug-settings-abstract.php';
		include_once $settings_dir . 'class-plugin-slug-settings-factory.php';
		include_once $settings_dir . 'class-plugin-slug-settings-general.php';
		include_once $settings_dir . 'class-plugin-slug-settings-demo.php';

		$this->settings = array(
			'General' => Plugin_Slug_Settings_Factory::get_instance('general'),
			// 'Demo' => Plugin_Slug_Settings_Factory::get_instance('demo'),
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
		$debug_mode = absint($this->settings['General']->get_fd_option('debug_mode'));
		if ($debug_mode === 1) {
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
		// 每一組的多個設定欄位
		foreach ($this->settings as $key => $obj) {
			$obj_setting = $obj->get_setting();
			$sec_page = $obj_setting['page'];

			if ($obj_setting['tab']['name'] === $active_tab) {
				$sections = $obj_setting['sections'];
				foreach ($sections as $section) {
					$sec_id = $section['id'];
					$sec_title = $section['title'];
					$sec_callback = $section['callback'];
					add_settings_section(
						$sec_id,			// ID of the settings section
						$sec_title,  		// Title of the section
						$sec_callback,		// Callback
						$sec_page			// ID of the page
					);
					$fds = $section['fields'];
					foreach ($fds as $fd_id => $fd_setting) {
						add_settings_field(
							$fd_id, 					// The ID of the settings field
							$fd_setting['title'], 		// The name of the field of setting(s)
							$fd_setting['callback'],
							$sec_page, 					// ID of the page on which to display these fields
							$sec_id, 					// The ID of the setting section
							array(
								'fd_id' => $fd_id, 
								'sec_id' => $sec_id
							)		// callback args
						);
						// TODO:如果設定欄位本身有指定 option_key，就自行儲存 (for 變更 WP,WC 內建設定 option 時)
						if (!empty($fd_setting['option_key'])){
							register_setting(
								$sec_page, 					// Group of options
								$fd_setting['option_key'], 	// Name of options
								array( $this, 'handle_before_save' )	// do sth before save (Sanitize function)
							);
						}
					}
				}
			}
			// 每一組的多個設定欄位，一次存進 option
			register_setting(
				$sec_page,								// Group of options
				$obj_setting['option_key'],				// Name of options
				array( $this, 'handle_before_save' )	//   do sth before save (Sanitize function)
			);
		}
	}

	// 將上傳的檔案存成 attachment
	private function upload_and_save_as_attachemnt($file_name, $tmp_name) {
		// upload
		$rst_upload = wp_upload_bits( $file_name, null, file_get_contents( $tmp_name ) );
		$uploaded_file_path = $rst_upload['file'];
		$wp_filetype = wp_check_filetype( basename( $uploaded_file_path ), null );
		$wp_filename = preg_replace('/\.[^.]+$/', '', basename( $uploaded_file_path ));
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $uploaded_file_path ),
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => $wp_filename,
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$uploaded_attach_id = wp_insert_attachment( $attachment, $uploaded_file_path );
		return $uploaded_attach_id;
	}

	/**
	 * Simple sanitize function
	 * @param $input
	 *
	 * @return array
	 */
	public function handle_before_save( $inputs ) {
		$new_inputs = '';
		do_action('plugin_slug_log', 'sanitize input', $inputs);

		// hidden
		$cur_option_key = $inputs['cur_option_key'];

		// 若有自行定義 submit btn，由此取得值
		$submit_btn = $inputs['demo_submit_btn'];

		// 若按下 demo_uplaod 的 submit button，就啟動資料上傳
		if ($submit_btn === 'demo_submit_upload') {
			// 有上傳檔案
			if (!empty($_FILES)) {
				$upload_filenames = $_FILES[$cur_option_key]['name']; // upload filenames
				// 若有多個上傳欄位，就會有多個 $fd_id
				foreach ($upload_filenames as $fd_id => $file_name) {
					// 上傳後的檔案會存成 attachment，另外再存下 meta: _attachment_fd_id，以此判斷屬於哪個 setting fd 
					// 若先前 setting fd 裡已有設定，就取出目前的設定
					$imp_args = array(
						'numberposts'      => 1,
						'orderby'          => 'date',
						'order'            => 'DESC',
						'meta_key'         => '_attachment_fd_id',
						'meta_value'       => $fd_id,
						'post_type'        => 'attachment'
					);
					// 清除目前的設定
					$imp_atts = get_posts($imp_args);
					foreach ($imp_atts as $att) {
						wp_delete_attachment($att->ID);
					}
					// 更新設定
					$tmp_name = $_FILES[$cur_option_key]['tmp_name'][$fd_id];
					if (!empty($file_name) && !empty($tmp_name)) {
						$attach_id = $this->upload_and_save_as_attachemnt($file_name, $tmp_name);
						update_post_meta($attach_id, '_attachment_fd_id', $fd_id);
					}
				}
				// do_action('plugin_slug_log', 'sanitize input', $_FILES);
			}
		}

		// 一個 setting tab 頁面如果有多組設定欄位，就會一次進到 inputs
		if (is_array($inputs)) {
			$new_inputs = array();
			foreach ( $inputs as $key => $val ) {
				$new_inputs[ $key ] = sanitize_text_field( $val );
			}
		}else{
			$new_inputs = sanitize_text_field($inputs);
		}

		// do_action('plugin_slug_log', 'sanitize output', $new_inputs);

		return $new_inputs;
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
		echo '<p>For adding data to log use the hook: <code>do_action( \'plugin_slug_log\', \'src_name\', $data );</code></p>';
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
				$cur_obj_setting = '';
				foreach ($this->settings as $key => $obj) {
					$obj_setting = $obj->get_setting();
					$tab_name = $obj_setting['tab']['name'];
					$sec_page = $obj_setting['page'];					
					if ($active_tab === $tab_name) {						
						settings_fields( $sec_page );
						do_settings_sections( $sec_page );
						$cur_obj_setting = $obj_setting;
					}
				}
				$submit_show = $cur_obj_setting['submit_show'];
				if ($submit_show) {
					submit_button($cur_obj_setting['submit_text']);
				}
				?>
			</form>
		</div><!-- .wrap -->
	<?php
	}
}