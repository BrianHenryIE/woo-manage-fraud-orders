<?php
/**
 * Main class
 * Handles everything from here, includes the file for the backend settings and
 * blacklisting funcitonalities, inlcudes the frontend handlers as well.
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Includes;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Admin;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Dependencies;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin\Plugins_Page;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\API\Track_Fraud_Attempts;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Bulk_Blacklist;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Order_Actions;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Order_MetaBox;
use PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce\Settings_Tab;

/**
 * Class Woo_Manage_Fraud_Orders
 */
class Woo_Manage_Fraud_Orders {

	/**
	 * The current plugin version.
	 *
	 * @var string $version
	 */
	protected $version = '2.3.1';

	/**
	 * Instantiate the class.
	 */
	public function __construct() {

		$this->set_locale();

		$this->define_admin_hooks();
		$this->define_dependencies_notice_hooks();
		$this->define_plugins_page_hooks();

		$this->define_bulk_blacklist_hooks();
		$this->define_track_fraud_attempts_hooks();

		$this->define_settings_tabs_hooks();
		$this->define_order_actions_hooks();
		$this->define_order_metabox_hooks();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	protected function set_locale() {

		$i18n = new I18n();

		add_action( 'plugins_loaded', array( $i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Define constants
	 */
	protected function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		$this->define( 'WMFO_ABSPATH', dirname( WMFO_PLUGIN_FILE ) . '/' );
		$this->define( 'WMFO_PLUGIN_BASENAME', plugin_basename( WMFO_PLUGIN_FILE ) );
		$this->define( 'WMFO_VERSION', $this->version );
		$this->define( 'WMFO_LOG_DIR', $upload_dir['basedir'] . '/wmfo-logs/' );
	}

	/**
	 * Define a constant if it has not already been defined .
	 *
	 * @param string $name The name of the constant to define .
	 * @param mixed  $value The value of the constant .
	 */
	protected function define( $name, $value )
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

	/*
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_admin_hooks() {

		$plugin_admin = new Admin();

		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
	}

	protected function define_plugins_page_hooks() {

		$plugins_page = new Plugins_Page();

		if ( defined( 'WMFO_PLUGIN_FILE' ) ) {
			$plugin_basename = plugin_basename( WMFO_PLUGIN_FILE );
		} else {
			$plugin_basename = 'woo-manage-fraud-orders/woo-manage-fraud-orders.php';
		}

		add_filter( 'plugin_action_links_' . $plugin_basename, array( $plugins_page, 'action_links' ), 99, 1 );
	}

	public function define_dependencies_notice_hooks() {
		$dependencies = new Dependencies();
		add_action( 'plugins_loaded', array( $dependencies, 'init_wp_dependency_installer' ) );
		add_filter( 'wp_dependency_dismiss_label', array( $dependencies, 'set_admin_notice_name' ), 10, 2 );
	}

	/**
	 * Check is WooCommerce active.
	 * Create log dir
	 * Create log db table
	 */
	public function install() {

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

		$this->may_be_create_log_dir_db_table();

	}

	/**
	 * Function to handle the creation of debug folder and DB table
	 */
	public function may_be_create_log_dir_db_table() {
		require_once plugin_dir_path( WMFO_PLUGIN_FILE ) . 'includes/class-wmfo-activator.php';

		WMFO_Activator::create_db_table();

		WMFO_Activator::create_upload_dir();

	}


	protected function define_bulk_blacklist_hooks() {

		$bulk_blacklist = new Bulk_Blacklist();

		add_filter( 'bulk_actions-edit-shop_order', array( $bulk_blacklist, 'register_bulk_action' ), 99, 1 );
		add_filter( 'handle_bulk_actions-edit-shop_order', array( $bulk_blacklist, 'handle_bulk_blacklisting' ), 10, 3 );
		add_action( 'admin_notices', array( $bulk_blacklist, 'print_admin_notice' ) );

	}

	protected function define_settings_tabs_hooks() {

		$setting_tab = new Settings_Tab();

		add_filter( 'woocommerce_settings_tabs_array', array( $setting_tab, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_blacklists', array( $setting_tab, 'settings_tab' ) );
		add_action( 'woocommerce_update_options_settings_tab_blacklists', array( $setting_tab, 'update_settings' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $setting_tab, 'update_setting_filter' ), 100, 3 );
	}

	protected function define_order_actions_hooks() {

		$order_actions = new Order_Actions();

		add_filter( 'woocommerce_order_actions', array( $order_actions, 'add_new_order_action' ), 99, 1 );
		add_action( 'woocommerce_process_shop_order_meta', array( $order_actions, 'update_blacklist' ), 60, 2 );
	}

	/**
	 *
	 * Order_MetaBox
	 *
	 * WMFO_Order_MetaBox constructor.
	 */
	protected function define_order_metabox_hooks() {

		$order_metabox = new Order_MetaBox();

		add_action( 'add_meta_boxes_shop_order', array( $order_metabox, 'add_meta_box' ), 99, 1 );
		add_action( 'save_post', array( $order_metabox, 'save_order_meta_box_data' ), 99, 1 );
	}

	public function define_track_fraud_attempts_hooks() {

		$track_fraud_attempts = new Track_Fraud_Attempts();

		add_action( 'woocommerce_after_checkout_validation', array( $track_fraud_attempts, 'manage_blacklisted_customers_checkout' ), 10, 2 );
		add_action( 'woocommerce_before_pay_action', array( $track_fraud_attempts, 'manage_blacklisted_customers_order_pay' ), 99, 1 );
		add_action( 'woocommerce_after_pay_action', array( $track_fraud_attempts, 'manage_multiple_failed_attempts_order_pay' ), 99, 1 );
		// Not part of WooCommerce core.
		add_action( 'woocommerce_api_wc_gateway_eway_payment_failed', array( $track_fraud_attempts, 'manage_multiple_failed_attempts_eway' ), 100, 4 );
		add_action( 'woocommerce_checkout_order_processed', array( $track_fraud_attempts, 'manage_multiple_failed_attempts_checkout' ), 100, 3 );
	}

	public function init_sub_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'WMFO Logs', 'woo-manage-fraud-orders' ),
			__( 'WMFO Logs', 'woo-manage-fraud-orders' ),
			'manage_options',
			'wmfo-logs',
			array( $this, 'render_logs' ),
			99999
		);
	}

	public function render_logs() {
		require_once plugin_dir_path( WMFO_PLUGIN_FILE ) . 'includes/admin/class-wmfo-logs-table.php';
		$logs = new WMFO_Logs_Table();
		$logs->prepare_items();
		?>
		<div class="wrap">
			<form method="post">
				<h2><?php _e( 'Logs of Blacklisted attempts.', 'woo-manage-fraud-orders' ); ?></h2>
				<p><?php _e( 'This is not the blacklisted customer details. Rather,  It is the list of customers who could not manage to place order due to blacklisting.', 'woo-manage-fraud-orders' ); ?></p>
				<?php $logs->display(); ?>
			</form>
		</div>
		<?php
	}

}
