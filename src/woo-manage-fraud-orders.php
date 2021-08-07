<?php
/**
 * Plugin Name:  Woo Manage Fraud Orders
 * Plugin URI:   https://prasidhda.com.np/how-to-blacklist-customers-from-placing-order-in-woocommerce/
 * Description:  Manage the fraudulent WooCommerce orders by blacklisting customers' details.
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

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Activator;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Deactivator;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Woo_Manage_Fraud_Orders;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/autoload.php';

define( 'WMFO_PLUGIN_FILE', __FILE__ );
define( 'WMFO_VERSION', '2.0.2' );
define( 'WMFO_PLUGIN_BASENAME', plugin_basename( WMFO_PLUGIN_FILE ) );

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

// Initialize the plugin.
$GLOBALS['woo_manage_fraud_orders'] = new Woo_Manage_Fraud_Orders();
