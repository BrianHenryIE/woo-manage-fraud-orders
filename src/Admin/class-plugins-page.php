<?php
/**
 * Add the `Settings` link on plugins.php.
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

/**
 * Class Plugins_Page
 */
class Plugins_Page {

	/**
	 * Add the `Settings` link under the plugin name on plugins.php.
	 *
	 * @hooked plugin_action_links_{plugin_basename}
	 * @see WP_Plugins_List_Table::single_row()
	 *
	 * @param array<string|int, string> $actions The existing registered links.
	 * @return array<string|int, string>
	 */
	public static function action_links( $actions ): array {

		$new_actions = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=settings_tab_blacklists' ) . '">' . __( 'Settings', 'woo-manage-fraud-orders' ) . '</a>',
		);

		return array_merge( $new_actions, $actions );
	}

}
