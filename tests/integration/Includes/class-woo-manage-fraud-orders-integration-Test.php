<?php
/**
 * Tests for Woo_Manage_Fraud_Orders main setup class. Tests the actions are correctly added.
 *
 * @package Woo_Manage_Fraud_Orders
 * @author  Prasidhda Malla <pranuk.prwnsi@gmail.com>
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Plugins_Page;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\API\Track_Fraud_Attempts;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Bulk_Blacklist;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Order_Actions;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Order_MetaBox;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Settings_Tab;

/**
 * Class Develop_Test
 * @coversNothing
 */
class Woo_Manage_Fraud_Orders_Integration_Test extends \Codeception\TestCase\WPTestCase {

    public function hooks() {
        $hooks = array(

//WMFO_Settings_Tab
//./includes/admin/class-wmfo-settings-tab.php:229:add_action( 'admin_head', closure )

            array( 'bulk_actions-edit-shop_order', Bulk_Blacklist::class, 'register_bulk_action', 99, 1 ),
            array( 'handle_bulk_actions-edit-shop_order', Bulk_Blacklist::class, 'handle_bulk_blacklisting', 10, 3 ),
            array( 'admin_notices', Bulk_Blacklist::class, 'print_admin_notice' ),

            array( 'woocommerce_order_actions', Order_Actions::class, 'add_new_order_action', 99, 1 ),
            array( 'woocommerce_process_shop_order_meta', Order_Actions::class, 'update_blacklist', 60, 2 ),

            array( 'add_meta_boxes_shop_order', Order_MetaBox::class, 'add_meta_box', 99, 1 ),
            array( 'save_post', Order_MetaBox::class, 'save_order_meta_box_data', 99, 1 ),

            array( 'woocommerce_settings_tabs_array', Settings_Tab::class, 'add_settings_tab', 50 ),
            array( 'woocommerce_settings_tabs_settings_tab_blacklists', Settings_Tab::class, 'settings_tab' ),
            array( 'woocommerce_update_options_settings_tab_blacklists', Settings_Tab::class, 'update_settings' ),
            array( 'woocommerce_admin_settings_sanitize_option', Settings_Tab::class, 'update_setting_filter', 100, 3 ),

            array( 'woocommerce_after_checkout_validation', Track_Fraud_Attempts::class, 'manage_blacklisted_customers_checkout', 10, 2 ),
            array( 'woocommerce_before_pay_action', Track_Fraud_Attempts::class, 'manage_blacklisted_customers_order_pay', 99, 1 ),
            array( 'woocommerce_after_pay_action', Track_Fraud_Attempts::class, 'manage_multiple_failed_attempts_order_pay', 99, 1 ),
            array( 'woocommerce_api_wc_gateway_eway_payment_failed', Track_Fraud_Attempts::class, 'manage_multiple_failed_attempts_eway', 100, 4 ),
            array( 'woocommerce_checkout_order_processed', Track_Fraud_Attempts::class, 'manage_multiple_failed_attempts_checkout', 100, 3 ),

            array( 'plugin_action_links_woo-manage-fraud-orders/woo-manage-fraud-orders.php', Plugins_Page::class, 'action_links', 99, 1 ),
            array( 'plugins_loaded', I18n::class, 'load_plugin_textdomain' ),
        );
        return $hooks;
    }

    /**
     * @dataProvider hooks
     */
    public function test_is_function_hooked_on_action( $action_name, $class_type, $method_name, $expected_priority = 10 ) {

        global $wp_filter;

        $this->assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

        $actions_hooked = $wp_filter[ $action_name ];

        $this->assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

        $hooked_method = null;
        foreach ( $actions_hooked[ $expected_priority ] as $action ) {
            $action_function = $action['function'];
            if ( is_array( $action_function ) ) {
                if ( $action_function[0] instanceof $class_type || is_string($action_function[0]) && $action_function[0] === $class_type ) {
                    if( $method_name === $action_function[1] ) {
                        $hooked_method = $action_function[1];
                        break;
                    }
                }
            }
        }

        $this->assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

        $this->assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );

    }
}
