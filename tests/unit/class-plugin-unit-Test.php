<?php
/**
 * Tests for the root plugin file.
 *
 * @package Woo_Manage_Fraud_Orders
 * @author  Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\Woo_Manage_Fraud_Orders;

/**
 * Class Plugin_WP_Mock_Test
 */
class Plugin_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	protected function _tearDown() {
		\WP_Mock::tearDown();
	}

	/**
	 * Verifies the plugin initialization.
     *
     * Can only `include` once.
	 */
	public function test_plugin_include() {

        // Prevents code-coverage counting, and removes the need to define the WordPress functions that are used in that class.
        \Patchwork\redefine(
            array( Woo_Manage_Fraud_Orders::class, '__construct' ),
            function() {}
        );

        $plugin_root_dir = dirname( __DIR__, 2 ) . '/src';

		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_root_dir . '/',
			)
		);

		\WP_Mock::userFunction(
			'register_activation_hook',
            array(
                'args'   => array(
                    \WP_Mock\Functions::type( 'string' ),
                    \WP_Mock\Functions::type( 'array' )
                )
            )
		);

		\WP_Mock::userFunction(
			'register_deactivation_hook',
            array(
                'args'   => array(
                    \WP_Mock\Functions::type( 'string' ),
                    \WP_Mock\Functions::type( 'array' )
                )
            )
		);


        ob_start();

        include $plugin_root_dir . '/woo-manage-fraud-orders.php';

        $printed_output = ob_get_contents();

        ob_end_clean();

        $this->assertEmpty( $printed_output );

		$this->assertArrayHasKey( 'woo_manage_fraud_orders', $GLOBALS );

		$this->assertInstanceOf( Woo_Manage_Fraud_Orders::class, $GLOBALS['woo_manage_fraud_orders'] );

	}

}
