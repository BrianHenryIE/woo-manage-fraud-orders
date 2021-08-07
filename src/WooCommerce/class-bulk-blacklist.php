<?php
/**
 * Class to handle the Bulk blacklisting of orders. i.e. on the WP_List_Table edit-orders screen.
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\API\Blacklist_Handler;
use function PrasidhdaMalla\Woo_Manage_Fraud_Orders\wmfo_get_customer_details_of_order;

/**
 * Class Bulk_Blacklist
 */
class Bulk_Blacklist {

	/**
	 * Add the "Blacklist Customer" action to the bulk actions drop-down above the shop orders list table.
	 *
	 * @hooked bulk_actions-edit-shop_order
	 *
	 * @see WP_List_Table::bulk_actions()
	 *
	 * @param array<string, string> $bulk_actions The bulk actions for the shop order list table.
	 * @return array<string, string>
	 */
	public function register_bulk_action( $bulk_actions ): array {
		$bulk_actions['blacklist-customer'] = __( 'Blacklist Customer', 'woo-manage-fraud-orders' );

		return $bulk_actions;
	}

	/**
	 * Execute the bulk blacklisting and return the URL to redirect to.
	 *
	 * @hooked handle_bulk_actions-edit-shop_order
	 *
	 * @see WP_List_Table::current_action()
	 *
	 * @param string $redirect_to The previously set/default redirect URL.
	 * @param string $action The current action.
	 * @param int[]  $post_ids The selected post ids (order ids).
	 * @return string The URL to redirect to.
	 * @throws \Exception
	 */
	public function handle_bulk_blacklisting( string $redirect_to, string $action, array $post_ids ): string {
		if ( 'blacklist-customer' !== $action ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			$order = wc_get_order( $post_id );

			if ( ! ( $order instanceof \WC_Order ) ) {
				continue;
			}

			// Get customer's IP address, billing phone and Email Address.
			$customer          = wmfo_get_customer_details_of_order( $order );
			$blacklist_handler = new Blacklist_Handler();
			// update the blacklists.
			if ( method_exists( 'WMFO_Blacklist_Handler', 'init' ) ) {
				$blacklist_handler->init( $customer, $order, 'add', 'back' );
			}
		}
		$redirect_to = wp_nonce_url(
			add_query_arg(
				array(
					'bulk_action' => $action,
					'changed'     => count( $post_ids ),
					'ids'         => join( ',', $post_ids ),
				),
				$redirect_to
			),
			'handle_bulk_blacklisting'
		);

		return $redirect_to;
	}

	/**
	 * If the bulk blacklisting action was just run, show an admin notice detailing the number of orders included.
	 *
	 * @see Bulk_Blacklist::handle_bulk_blacklisting()
	 *
	 * @hooked admin_notices
	 */
	public function print_admin_notice() {
		global $post_type, $pagenow;
		// Bail out if not on shop order list page.
		if ( 'edit.php' !== $pagenow || 'shop_order' !== $post_type ) { // WPCS: input var ok, CSRF ok.
			return;
		}

		if ( ! wp_verify_nonce( 'handle_bulk_blacklisting' ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['bulk_action'] ) ) {
			return;
		}

		if ( 'blacklist-customer' !== $_REQUEST['bulk_action'] ) {
			return;
		}

		$orders_count = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;
		?>
		<div id="message" class="updated fade">
			<?php
				echo esc_html(
					sprintf(
						// translators: a number of orders.
						_n(
							'%d order has been affected by bulk blacklisting.',
							'%d orders have been affected by bulk blacklisting.',
							$orders_count,
							'woo-manage-fraud-orders'
						),
						$orders_count
					)
				);
			?>
		</div>
		<?php
	}
}
