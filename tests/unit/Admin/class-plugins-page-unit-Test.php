<?php
/**
 * Verify the settings link is added to plugins.php.
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

/**
 *
 * @coversDefaultClass  \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Plugins_Page
 */
class Plugins_Page_Unit_Test extends \Codeception\Test\Unit {

    protected function _before() {
        \WP_Mock::setUp();
    }

    // This is required for `'times' => 1` to be verified.
    protected function _tearDown() {
        parent::_tearDown();
        \WP_Mock::tearDown();
    }

    /**
     * @covers ::action_links
     */
    public function test_action_links() {

        $sut = new Plugins_Page();

        \WP_Mock::userFunction(
            'admin_url',
            array(
                'args'   => array( 'admin.php?page=wc-settings&tab=settings_tab_blacklists' ),
                'return' => 'admin.php?page=wc-settings&tab=settings_tab_blacklists',
            )
        );

        $result = $sut->action_links( array() );

        $this->assertEquals( '<a href="admin.php?page=wc-settings&tab=settings_tab_blacklists">Settings</a>', array_values( $result )[0] );

    }

}
