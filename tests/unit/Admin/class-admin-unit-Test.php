<?php
/**
 * Tests for Admin.
 *
 * @see Admin
 *
 * @package woo-manage-fraud-orders
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

use WP_Mock\Functions;

/**
 * Class Admin_Test
 *
 * @coversDefaultClass \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Admin
 */
class Admin_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verifies enqueue_styles() calls wp_enqueue_style() with appropriate parameters.
	 * Verifies the .css file exists.
	 *
	 * @covers ::enqueue_styles
	 * @see wp_enqueue_style()
	 */
	public function test_enqueue_styles() {

		global $plugin_root_dir;

        $plugin_name = 'woo-manage-fraud-orders';

		// Return any old url.
		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => $plugin_root_dir . '/admin/',
			)
		);

		$css_file = $plugin_root_dir . '/admin/css/woo-manage-fraud-orders-admin.css';

		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times' => 1,
				'args'  => array( $plugin_name, $css_file, array(), Functions::type( 'string' ), 'all' ),
			)
		);

		$admin = new Admin();

		$admin->enqueue_styles();

		$this->assertFileExists( $css_file );
	}

}
