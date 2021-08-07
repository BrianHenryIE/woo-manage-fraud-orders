<?php
/**
 * Class to handle the updating of blacklists while editing the order page
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce;

use PrasidhdaMalla\Woo_Manage_Fraud_Orders\API\Blacklist_Handler;
use function PrasidhdaMalla\Woo_Manage_Fraud_Orders\wmfo_get_customer_details_of_order;

/**
 * Class Order_Actions
 */
class Order_Actions {

	const BLACK_LIST_ORDER_ACTION             = 'black_list_order';
	const REMOVE_FROM_BLACK_LIST_ORDER_ACTION = 'remove_from_black_list';

	/**
	 * Add an order action. i.e. on the single order edit screen beside the "update" button.
	 *
	 * @hooked woocommerce_order_actions
	 * @see WC_Meta_Box_Order_Actions::output()
	 *
	 * @param array<string, string> $order_actions The existing order actions.
	 *
	 * @return array<string, string>
	 */
	public function add_new_order_action( $order_actions ): array {
		// Show this only if customer details of this order is in blacklist.

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : null;

		if ( ! empty( $action ) && ! empty( $order_id ) ) {
			$order = wc_get_order( $order_id );

			if ( ! ( $order instanceof \WC_Order ) ) {
				return $order_actions;
			}

			if ( 'shop_order' !== $order->get_type() ) {
				return $order_actions;
			}

			$order_actions[ self::BLACK_LIST_ORDER_ACTION ] = __( 'Blacklist order', 'woo-manage-fraud-orders' );
			$blacklist_handler                              = new Blacklist_Handler();
			// Check if the order details of this current order is in blacklist.
			$customer = wmfo_get_customer_details_of_order( $order );
			if ( false !== $customer && $blacklist_handler->is_blacklisted( $customer ) ) {
				$order_actions[ self::REMOVE_FROM_BLACK_LIST_ORDER_ACTION ] = __( 'Remove from Blacklist', 'woo-manage-fraud-orders' );
			}
		}

		return $order_actions;
	}

	/**
	 * Execute the order action (Add|Remove from blacklist).
	 *
	 * @hooked woocommerce_process_shop_order_meta
	 * @see WC_Admin_Meta_Boxes::save_meta_boxes()
	 * @see save_post
	 *
	 * @param int      $post_id The post id, i.e. the order id, being saved.
	 * @param \WP_Post $post The WordPress post being saved.
	 *
	 * @throws \Exception
	 */
	public function update_blacklist( $post_id, $post ) {
		$order = wc_get_order( $post_id );

		if ( ! ( $order instanceof \WC_Order ) ) {
			return;
		}

		$blacklist_handler = new Blacklist_Handler();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$action = isset( $_POST['wc_order_action'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_order_action'] ) ) : null;
		if ( ! empty( $action ) ) {
			// Get customer's IP address, billing phone and Email Address.
			$customer = wmfo_get_customer_details_of_order( $order );
			// Add the customer details to Blacklist.
			if ( self::BLACK_LIST_ORDER_ACTION === $action ) {
				// update the blacklists.

				$blacklist_handler->init( $customer, $order, 'add', 'back' );

			} elseif ( self::REMOVE_FROM_BLACK_LIST_ORDER_ACTION === $action ) {
				// Remove the customer details from blacklist.
				// update the blacklists.

				$blacklist_handler->init( $customer, $order, 'remove', 'back' );

			}
		}
	}
}
