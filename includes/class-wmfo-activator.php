<?php

/**
 * Fired during plugin activation.
 *
 */
class WMFO_Activator {

    public static function create_db_table() {
	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
	    $table = $wpdb->prefix . 'wmfo_logs';

	    if ( !($wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) === $table) ) {
		    $charset_collate = $wpdb->get_charset_collate();
		    $sql = "CREATE TABLE $table (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				first_name varchar(255) DEFAULT '' NOT NULL,
				last_name varchar(255) DEFAULT '' NOT NULL,
				phone varchar(255) DEFAULT '' NOT NULL,
				ip varchar(255) DEFAULT '' NOT NULL,
				email varchar(255) DEFAULT '' NOT NULL,
				billing_address varchar(255) DEFAULT '' NOT NULL,
				shipping_address varchar(255) DEFAULT '' NOT NULL,
				timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

		    dbDelta($sql);

		    flush_rewrite_rules();
	    }

    }

	public static function create_upload_dir(){
		if ( !is_dir(WMFO_LOG_DIR) ) {
			mkdir(WMFO_LOG_DIR, 0700);
		}
	}

}
