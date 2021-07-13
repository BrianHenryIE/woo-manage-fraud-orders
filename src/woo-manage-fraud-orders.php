<?php
/**
 * Plugin Name:  Woo Manage Fraud Orders
 * Plugin URI:   https://prasidhda.com.np/how-to-blacklist-customers-from-placing-order-in-woocommerce/
 * Description:  WooCommerce plugin to manage the fraud orders by blacklisting the customer's details.
 * Version:      2.3.1
 * Author:       Prasidhda Malla
 * Author URI:   https://prasidhda.com.np/
 * License:      GPLv2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  woo-manage-fraud-orders
 * WC requires at least: 2.6
 * WC tested up to: 5.5.2
 *
 * @package woo-manage-fraud-orders
 */

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Woo_Manage_Fraud_Orders;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! defined( 'WMFO_PLUGIN_FILE' ) ) {
	define( 'WMFO_PLUGIN_FILE', __FILE__ );
}

if ( ! class_exists( 'Woo_Manage_Fraud_Orders' ) ) {
	include_once dirname( __FILE__ ) . '/Includes/class-woo-manage-fraud-orders.php';
}

require_once __DIR__ . '/autoload.php';

// Initialize the plugin.
$GLOBALS['woo_manage_fraud_orders'] = new Woo_Manage_Fraud_Orders();


/**
 * Admin Styling
 */
add_action(
	'admin_head',
	function () {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['tab'] ) && 'settings_tab_blacklists' === $_GET['tab'] ) : ?>
			<style>
				.wrap.woocommerce .forminp.forminp-multiselect span.description {
					display: block;
					padding: 10px 0 0;
				}
			</style>
			<?php
		endif;
	}
);
