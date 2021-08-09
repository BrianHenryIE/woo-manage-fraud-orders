<?php
/**
 * Runs upgrade jobs once.
 * Checks version number recorded in database.
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

class Upgrader {

	public function upgrade() {

		$new_vesion = '2.3.1';
		$version    = get_option( 'woo_manage_fraud_orders_version', '0.0.0' );

		if ( version_compare( $version, '2.3.1', '<' ) ) {
			$this->create_db_table();
		}

		$this->create_upload_dir();

		update_option( 'woo_manage_fraud_orders_version', $new_vesion );
	}


	/**
	 * @since
	 */
	public function create_db_table() {
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
	 * Since an admin could delete the folder at any time, it needs to be checked before
	 * writing the log file itself.
	 */
	public function create_upload_dir() {
		if ( ! is_dir( WMFO_LOG_DIR ) ) {
			mkdir( WMFO_LOG_DIR, 0700 );
		}
	}

}
