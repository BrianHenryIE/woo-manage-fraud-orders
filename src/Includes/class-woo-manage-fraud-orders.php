<?php
/**
 * Main class
 * Handles everything from here, includes the file for the backend settings and
 * blacklisting funcitonalities, inlcudes the frontend handlers as well.
 *
 * @package woo-manage-fraud-orders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Woo_Manage_Fraud_Orders' ) ) {

	/**
	 * Class Woo_Manage_Fraud_Orders
	 */
	class Woo_Manage_Fraud_Orders {

		/**
		 * The current plugin version.
		 *
		 * @var string $version
		 */
		public $version = '2.0.2';

		/**
		 * Store the class singleton.
		 *
		 * @var ?Woo_Manage_Fraud_Orders
		 */
		protected static $instance = null;

		/**
		 * Instantiate the class.
		 */
		protected function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Get an instance of the class.
		 *
		 * @return Woo_Manage_Fraud_Orders
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Define constants
		 */
		private function define_constants() {
			$upload_dir = wp_upload_dir( null, false );

			$this->define( 'WMFO_ABSPATH', dirname( WMFO_PLUGIN_FILE ) . '/' );
			$this->define( 'WMFO_PLUGIN_BASENAME', plugin_basename( WMFO_PLUGIN_FILE ) );
			$this->define( 'WMFO_VERSION', $this->version );
			$this->define( 'WMFO_LOG_DIR', $upload_dir['basedir'] . '/wmfo-logs/' );
		}

		/**
		 * Define a constant if it has not already been defined.
		 *
		 * @param string $name The name of the constant to define.
		 * @param mixed  $value The value of the constant.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Init hooks
		 */
		private function init_hooks() {
			register_activation_hook( WMFO_PLUGIN_FILE, array( $this, 'install' ) );

			$plugins_page = new Plugins_Page();
			add_filter( 'plugin_action_links_' . plugin_basename( WMFO_PLUGIN_FILE ), array( $plugins_page, 'action_links' ), 99, 1 );
			$i18n = new I18n();
			add_action( 'plugins_loaded', array( $i18n, 'load_plugin_textdomain' ) );
		}

		/**
		 * Check is WooCommerce active.
		 */
		public function install() {
			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

				echo sprintf( esc_html__( 'Woo Manage Fraud Orders depends on %s to work!', 'woo-manage-fraud-orders' ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">' . esc_html__( 'WooCommerce', 'woo-manage-fraud-orders' ) . '</a>' );
				@trigger_error( '', E_USER_ERROR );

			}
		}


		/**
		 * Include required files.
		 */
		public function includes() {
			require_once WMFO_ABSPATH . 'Includes/wmfo-functions.php';
			require_once WMFO_ABSPATH . 'Includes/class-wmfo-blacklist-handler.php';
			require_once WMFO_ABSPATH . 'Includes/class-wmfo-track-fraud-attempts.php';
			// if ( is_admin() ) {
				require_once WMFO_ABSPATH . 'WooCommerce/class-wmfo-settings-tab.php';
				require_once WMFO_ABSPATH . 'WooCommerce/class-wmfo-order-metabox.php';
				require_once WMFO_ABSPATH . 'WooCommerce/class-wmfo-order-actions.php';
				require_once WMFO_ABSPATH . 'WooCommerce/class-wmfo-bulk-blacklist.php';
			// }
		}

	}
}
