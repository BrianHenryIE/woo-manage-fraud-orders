<?php
/**
 * Register the languages directory with WordPress.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

/**
 * Class I18n
 */
class I18n {

	/**
	 * Load text domain for translation
	 *
	 * @hooked plugins_loaded
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'woo-manage-fraud-orders',
			false,
			plugin_basename( dirname( __FILE__, 2 ) ) . '/languages/'
		);
	}

}