<?php
/**
 * PHPUnit bootstrap file for wpunit tests. Since the plugin will not be otherwise autoloaded.
 *
 * @package           Woo_Manage_Fraud_Orders
 */

global $plugin_root_dir;
require_once $plugin_root_dir . '/autoload.php';

activate_plugin( 'woocommerce/woocommerce.php' );
