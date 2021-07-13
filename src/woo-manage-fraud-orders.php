<?php
/**
 * Plugin Name:  Woo Manage Fraud Orders
 * Plugin URI:   https://prasidhda.com.np/how-to-blacklist-customers-from-placing-order-in-woocommerce/
 * Description:  Manage the fraudulent WooCommerce orders by blacklisting customers' details.
 * Version:      3.0
 * Author:       Prasidhda Malla
 * Author URI:   https://prasidhda.com.np/
 * License:      GPLv2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  woo-manage-fraud-orders
 * WC requires at least: 2.6
 * WC tested up to: 5.4.1
 *
 * @package woo-manage-fraud-orders
 */

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Woo_Manage_Fraud_Orders;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WMFO_PLUGIN_FILE', __FILE__ );
define( 'WMFO_VERSION', '2.0.2' );

require_once __DIR__ . '/autoload.php';

// Initialize the plugin.
$GLOBALS['woo_manage_fraud_orders'] = new Woo_Manage_Fraud_Orders();
