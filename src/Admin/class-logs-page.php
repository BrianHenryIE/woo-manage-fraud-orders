<?php

namespace PrasidhdaMalla\Woo_Manage_Fraud_Orders\Admin;

class Logs_Page {

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
		$logs = new Logs_Table();
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
