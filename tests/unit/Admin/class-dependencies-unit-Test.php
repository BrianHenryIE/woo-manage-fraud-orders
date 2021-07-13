<?php
/**
 * Tests for Dependencies.
 *
 * @see \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Dependencies
 *
 * @package woo-manage-fraud-orders
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

use WP_Mock\Functions;

/**
 * Class Dependencies_Unit_Test
 *
 * @coversDefaultClass \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Dependencies
 */
class Dependencies_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

    public function test_admin_notice_plugin_name() {

        $dependenceies = new Dependencies();

        $label = 'existing label';
        $source = 'woo-manage-fraud-orders';

        $result = $dependenceies->set_admin_notice_name( $label, $source );

        $this->assertEquals( 'Woo Manage Fraud Orders', $result );
    }

    public function test_admin_notice_other_plugin_name() {

        $dependenceies = new Dependencies();

        $label = 'existing label';
        $source = 'another-plugin';

        $result = $dependenceies->set_admin_notice_name( $label, $source );

        $this->assertEquals( 'existing label', $result );
    }
}
