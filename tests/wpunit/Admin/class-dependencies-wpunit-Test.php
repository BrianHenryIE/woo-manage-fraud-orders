<?php

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

/**
 * Class Dependencies_WPUnit_Test
 * @package PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin
 * @coversDefaultClass \PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Dependencies
 */
class Dependencies_WPUnit_Test extends \Codeception\TestCase\WPTestCase {

    public function test_register_dependencies() {

        $sut = new Dependencies();

        $sut->init_wp_dependency_installer();

        $dependency_installer_instance = \PM_Woo_Manage_Fraud_Orders_WP_Dependency_Installer::instance();
        $config = $dependency_installer_instance->get_config();

        $this->assertEquals( 'WooCommerce', array_values($config)[0]['name'] );

    }

}
