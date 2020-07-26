<?php
abstract class [plugin_slug_classname]_Settings_Abstract{

	abstract protected function get_setting();

	// get data from DB
	public function get_fd_option( $key = '' ) {
		$obj_setting = $this->get_setting();
		$opts = get_option( $obj_setting['option_key'] );
		if (!empty($key)) {
			if (!empty($opts[$key])) {
				return $opts[$key];
			}else{
				return '';
			}
		}
		return $opts;
	}
}

class [plugin_slug_classname]_Settings_Factory {
    public function __construct() {
	}

	public static function get_instance($setting_type) {
		switch ($setting_type) {
			case 'general':
				return [plugin_slug_classname]_Settings_General::get_instance();
				break;
			// case 'others':
			// 	return [plugin_slug_classname]_Settings_Others::get_instance();
			// 	break;
			default:
				return false;
				break;
		}
	}
}
