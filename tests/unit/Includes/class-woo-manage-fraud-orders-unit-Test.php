<?php
/**
 * @package Woo_Manage_Fraud_Orders_Unit_Name
 * @author  Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

use WP_Mock\Matcher\AnyInstance;

/**
 * Class Woo_Manage_Fraud_Orders_Unit_Test
 * @coversDefaultClass \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Woo_Manage_Fraud_Orders
 */
class Woo_Manage_Fraud_Orders_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	// This is required for `'times' => 1` to be verified.
	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::set_locale
	 */
	public function test_set_locale_hooked() {

	    global $plugin_basename;

		\WP_Mock::expectActionAdded(
			'plugins_loaded',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

        \WP_Mock::userFunction(
            'plugin_basename',
            array(
                'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
                'return' => $plugin_basename
            )
        );

		new Woo_Manage_Fraud_Orders();
	}

}
