<?php
/**
 * @package Woo_Manage_Fraud_Orders_Unit_Name
 * @author  Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\API\Track_Fraud_Attempts;
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

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

		new Woo_Manage_Fraud_Orders();
	}

    /**
     * @covers ::define_track_fraud_attempts_hooks
     */
	public function test_track_fraud_attempts_hooks(): void {

        \WP_Mock::expectActionAdded(
            'woocommerce_after_checkout_validation',
            array( new AnyInstance( Track_Fraud_Attempts::class ), 'manage_blacklisted_customers_checkout' ),
            10,
            2
        );

        \WP_Mock::expectActionAdded(
            'woocommerce_before_pay_action',
            array( new AnyInstance( Track_Fraud_Attempts::class ), 'manage_blacklisted_customers_order_pay' ),
            99,
            1
        );

        \WP_Mock::expectActionAdded(
            'woocommerce_after_pay_action',
            array( new AnyInstance( Track_Fraud_Attempts::class ), 'manage_multiple_failed_attempts_order_pay' ),
            99,
            1
        );

        \WP_Mock::expectActionAdded(
            'woocommerce_api_wc_gateway_eway_payment_failed',
            array( new AnyInstance( Track_Fraud_Attempts::class ), 'manage_multiple_failed_attempts_eway' ),
            100,
            4
        );

        \WP_Mock::expectActionAdded(
            'woocommerce_checkout_order_processed',
            array( new AnyInstance( Track_Fraud_Attempts::class ), 'manage_multiple_failed_attempts_checkout' ),
            100,
            3
        );

        new Woo_Manage_Fraud_Orders();

    }
}
