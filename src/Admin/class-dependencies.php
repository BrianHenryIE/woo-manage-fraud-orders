<?php
/**
 * Configuration for WP Dependency Installer.
 *
 * @see https://github.com/afragen/wp-dependency-installer/wiki/Configuration
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

class Dependencies {

	/**
	 * @var array<array>
	 */
	protected static $plugins = array(
		array(
			'name'     => 'WooCommerce',
			'host'     => 'wordpress',
			'slug'     => 'woocommerce/woocommerce.php',
			'uri'      => 'https://wordpress.org/plugins/woocommerce/',
			'required' => false,
		),
	);

	/**
	 * @hooked plugins_loaded
	 */
	public function init_wp_dependency_installer(): void {

		\PM_Woo_Manage_Fraud_Orders_WP_Dependency_Installer::instance( __DIR__ )
			->register( self::$plugins )
			->run();
	}

	/**
	 * Set the plugin name in the admin notices for required plugins.
	 * Otherwise it infers a name from the directory.
	 *
	 * @hooked wp_dependency_dismiss_label
	 *
	 * @param string $label
	 * @param string $source
	 * @return string
	 */
	public function set_admin_notice_name( $label, $source ): string {
		$label = basename( __DIR__ ) !== $source ? $label : 'Woo Manage Fraud Orders';
		return $label;
	}

}
