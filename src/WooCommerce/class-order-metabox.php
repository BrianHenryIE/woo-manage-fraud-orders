<?php
/**
 * Adds an option on pending orders to skip the blacklist check:
 * "Check this to bypass this order payment from blacklisting".
 *
 * @package woo-manage-fraud-orders
 */

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\WooCommerce;

/**
 * Class Order_MetaBox
 */
class Order_MetaBox {

	/**
	 * When an order's status is pending, register a metabox with the option:
	 * "Check this to bypass this order payment from blacklisting.".
	 *
	 * @hooked add_meta_boxes_shop_order
	 * @see register_and_do_post_meta_boxes()
	 *
	 * @param \WP_Post $post The post object currently being edited.
	 */
	public function add_meta_box( $post ) {
		$order = wc_get_order( $post->ID );

		if ( ! ( $order instanceof \WC_Order ) ) {
			return;
		}

		if ( 'pending' !== $order->get_status() ) {
			return;
		}

		add_meta_box(
			'wmfo-order-metabox',
			__( 'WMFO', 'woo-manage-fraud-orders' ),
			array( $this, 'print_actions_meta_box' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Output the HTML for the metabox. A checkbox:
	 * "Check this to bypass this order payment from blacklisting".
	 *
	 * @param \WP_Post $post The post object currently being edited.
	 */
	public function print_actions_meta_box( $post ) {
		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'wmfo_skip_blacklisting_nonce', 'wmfo_skip_blacklist_nonce' );

		$value = get_post_meta( $post->ID, 'wmfo_skip_blacklist', true );
		$html  = '<label for="wmfo_skip_blacklist">' . __( 'Check this to bypass this order payment from blacklisting.', 'woo-manage-fraud-orders' ) . '</label>';
		$html .= '<input type="checkbox" name="wmfo_skip_blacklist" id="wmfo_skip_blacklist" style="margin: 4px 12px 0;"' . checked( $value, 'yes', false ) . ' value="yes" />';

		echo wp_kses(
			$html,
			array(
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'type'    => array(),
					'name'    => array(),
					'id'      => array(),
					'style'   => array(),
					'checked' => array(),
					'value'   => array(),
				),
			)
		);
	}

	/**
	 * When the post is saved, save our custom data.
	 *
	 * @hooked save_post
	 * @see wp_insert_post()
	 * @see wp_publish_post()
	 *
	 * @param int $post_id The id of the post (order) being edited.
	 */
	public function save_order_meta_box_data( int $post_id ) {

		// Verify that the nonce is valid.
		if ( ! isset( $_POST['wmfo_skip_blacklist_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wmfo_skip_blacklist_nonce'] ) ), 'wmfo_skip_blacklisting_nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Sanitize input.
		$wmfo_skip_blacklist = isset( $_POST['wmfo_skip_blacklist'] ) ? sanitize_text_field( wp_unslash( $_POST['wmfo_skip_blacklist'] ) ) : null;

		if ( empty( $wmfo_skip_blacklist ) ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, 'wmfo_skip_blacklist', $wmfo_skip_blacklist );
	}
}
