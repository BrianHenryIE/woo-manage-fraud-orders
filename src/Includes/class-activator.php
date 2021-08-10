<?php
/**
 * Fired during plugin activation.
 *
 * NB: Activation only runs the on plugin activation, it does not run when plugins are upgraded.
 *
 * @see https://developer.wordpress.org/reference/functions/register_activation_hook/#comment-2100
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

/**
 * Class Activator
 *
 * @package PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes
 */
class Activator {

	/**
	 *
	 * Was "maybe_create_log_dir_db_table".
	 *
	 * @hooked register_activation_hook
	 */
	public static function activate() {
		self::install();
	}


	/**
	 * Check is WooCommerce active.
	 * Create log db table
	 * Create log dir
	 */
	public static function install() {

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// multisite
		if ( is_multisite() ) {
			// this plugin is network activated - Woo must be network activated
			if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				$need = ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' );
				// this plugin is locally activated - Woo can be network or locally activated
			} else {
				$need = ! is_plugin_active( 'woocommerce/woocommerce.php' );
			}
			// this plugin runs on a single site
		} else {
			$need = ! is_plugin_active( 'woocommerce/woocommerce.php' );
		}

		if ( $need ) {

			echo sprintf( esc_html__( 'Woo Manage Fraud Orders depends on %s to work!', 'woo-manage-fraud-orders' ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">' . esc_html__( 'WooCommerce', 'woo-manage-fraud-orders' ) . '</a>' );
			@trigger_error( '', E_USER_ERROR );
		}

	}

}
