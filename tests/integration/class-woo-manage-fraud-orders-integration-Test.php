<?php
/**
 * Class Plugin_Test. Tests the root plugin setup.
 *
 * @package Woo_Manage_Fraud_Orders
 * @author     Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Woo_Manage_Fraud_Orders;

/**
 * Verifies the plugin has been instantiated and added to PHP's $GLOBALS variable.
 * @coversNothing
 */
class Plugin_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Test the main plugin object is added to PHP's GLOBALS and that it is the correct class.
	 */
	public function test_plugin_instantiated() {

		$this->assertArrayHasKey( 'woo_manage_fraud_orders', $GLOBALS );

		$this->assertInstanceOf( Woo_Manage_Fraud_Orders::class, $GLOBALS['woo_manage_fraud_orders'] );
	}

}
