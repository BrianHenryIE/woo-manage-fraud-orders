<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Woo_Manage_Fraud_Orders
 * @subpackage Woo_Manage_Fraud_Orders/admin
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Manage_Fraud_Orders
 * @subpackage Woo_Manage_Fraud_Orders/admin
 * @author     Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */
class Admin {

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * Previously this was printed inline, ocnditionally if we were on the correct settings tab.
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$version = defined( 'WMFO_VERSION' ) ? WMFO_VERSION : '' . time();

		wp_enqueue_style( 'woo-manage-fraud-orders', plugin_dir_url( __FILE__ ) . 'css/woo-manage-fraud-orders-admin.css', array(), $version, 'all' );

	}

}
