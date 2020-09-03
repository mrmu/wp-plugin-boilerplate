<?php
class Plugin_Slug_Settings_Factory {
    public function __construct() {
	}

	public static function get_instance($setting_type) {
		switch ($setting_type) {
			case 'general':
				return Plugin_Slug_Settings_General::get_instance();
				break;
			case 'demo':
				return Plugin_Slug_Settings_Demo::get_instance();
				break;
			default:
				return false;
				break;
		}
	}
}
