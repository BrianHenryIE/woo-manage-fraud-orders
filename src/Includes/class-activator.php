<?php
/**
 * Fired during plugin activation.
 *
 * NB: Activation only runs the on plugin activation, it does not run when plugins are upgraded.
 * @see https://developer.wordpress.org/reference/functions/register_activation_hook/#comment-2100
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

/**
 * Class Activator
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
		self::create_db_table();
		self::create_upload_dir();
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

	/**
	 */
	public static function create_db_table() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;
		$table = $wpdb->prefix . 'wmfo_logs';

		if ( ! ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) ) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql             = "CREATE TABLE $table (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				full_name varchar(255) DEFAULT '' NOT NULL,
				phone varchar(255) DEFAULT '' NOT NULL,
				ip varchar(255) DEFAULT '' NOT NULL,
				email varchar(255) DEFAULT '' NOT NULL,
				billing_address varchar(255) DEFAULT '' NOT NULL,
				shipping_address varchar(255) DEFAULT '' NOT NULL,
				blacklisted_reason varchar(255) DEFAULT '' NOT NULL,
				timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			dbDelta( $sql );

			flush_rewrite_rules();
		}

	}

	/**
	 * This is in the wrong place. Since an admin could delete the folder at any time, it needs to be checked before
	 * writing the log file itself.
	 */
	public static function create_upload_dir() {
		if ( ! is_dir( WMFO_LOG_DIR ) ) {
			mkdir( WMFO_LOG_DIR, 0700 );
		}
	}

}
