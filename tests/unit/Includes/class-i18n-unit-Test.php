<?php
/**
 *
 *
 * @package Woo_Manage_Fraud_Orders
 * @author  Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

/**
 * Class Plugin_WP_Mock_Test
 *
 * @covers \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes\I18n
 */
class I18n_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verify load_plugin_textdomain is correctly called.
	 *
	 * @covers ::load_plugin_textdomain
	 */
	public function test_load_plugin_textdomain() {

        global $plugin_root_dir;

        \WP_Mock::userFunction(
            'plugin_basename',
            array(
                'args'   => array(
                    \WP_Mock\Functions::type( 'string' )
                ),
                'return' => 'woo-manage-fraud-orders',
                'times' => 1
            )
        );

        \WP_Mock::userFunction(
            'load_plugin_textdomain',
            array(
                'times' => 1,
                'args'   => array(
                    'woo-manage-fraud-orders',
                    false,
                    'woo-manage-fraud-orders/languages/',
                )
            )
        );

        $i18n = new I18n();
        $i18n->load_plugin_textdomain();
	}
}
