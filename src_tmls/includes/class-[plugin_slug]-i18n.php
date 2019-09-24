<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       [AUTHOR_URI]
 * @since      1.0.0
 *
 * @package    [plugin_slug_classname]
 * @subpackage [plugin_slug_classname]/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    [plugin_slug_classname]
 * @subpackage [plugin_slug_classname]/includes
 * @author     [AUTHOR_NAME] <[AUTHOR_EMAIL]>
 */
class [plugin_slug_classname]_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'[plugin_slug]',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
