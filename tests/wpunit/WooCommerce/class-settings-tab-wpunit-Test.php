<?php
/**
 * Runs the settings functions to catch any fatal errors.
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce;

/**
 * Class Settings_Tab_WPUnit_Test
 * @package PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce
 * @coversDefaultClass \PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Settings_Tab
 */
class Settings_Tab_WPUnit_Test extends \Codeception\TestCase\WPTestCase
{

    /**
     * When refactoring, this function was crashing.
     */
    public function test_get_settings_not_crashing() {

        $sut = new Settings_Tab();

        $result = $sut->get_settings();

        $this->assertEquals( 'Blacklisted Customers', $result['section_title']['name'] );
    }

}