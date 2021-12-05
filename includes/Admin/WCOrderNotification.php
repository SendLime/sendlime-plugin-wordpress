<?php

namespace SendLime\SendLime\Admin;

/**
 * WooCommerce order notification handler
 */
class WCOrderNotification {

	public function plugin_page() {
		$template = __DIR__ . '/views/wc-order-notification.php';

		if ( file_exists( $template ) ) {
			include $template;
		}
	}

	public function wc_order_status_change_handler( $order_id ) {
		$settings = get_option( SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY );

		if ( ! $settings['api_key'] || ! $settings['api_secret'] || ! $settings['enabled'] ) return;

		$order_details = wc_get_order( $order_id );

		if ( ! $order_details->get_billing_phone() ) return;

		$enabled_statuses = $settings['status'];
		$current_status = 'wc-' . $order_details->get_status();

		if ( ! in_array( $current_status, $enabled_statuses ) ) return;
		if ( ! $settings[$current_status] ) return;

		$text = sendlime_process_order_message( $settings[$current_status], $order_details );
		$to = sendlime_process_phone_number( $order_details->get_billing_phone() );

		$body = array(
			'api_key'       => $settings['api_key'],
			'api_secret'    => $settings['api_secret'],
			'from'          => $settings['from'],
			'to'            => $to,
			'text'          => $text
		);

		sendlime_send_sms($body);
	}

	/**
	 * Handle the form
	 *
	 * @return void
	 */
	public function form_handler() {
		global $sendlime_wc_order_notification_settings;

		if ( ! isset( $_POST['wc_order_notification_settings'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wc-order-notification-settings' ) ) {
			wp_die( 'Are you cheating?' );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Are you cheating?' );
		}

		unset($_POST['_wpnonce']);
		unset($_POST['_wp_http_referer']);
		unset($_POST['wc_order_notification_settings']);

		if ( ! $_POST['enabled'] ) {
			$_POST['enabled'] = false;
		} else {
			$_POST['enabled'] = true;
		}

		sendlime_wc_update_order_notification_settings($_POST);

		$redirect_to = admin_url( 'admin.php?page=sendlime&saved=true' );
		wp_redirect( $redirect_to );
		exit;
	}

	public function wc_statuses() {
		$statuses = wc_get_order_statuses();
		$settings = get_option( SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY );
		$selected_statuses = $settings['status'];

		foreach ($statuses as $status => $value) {
			$checked = '';
			if ( in_array( $status, $selected_statuses ) ) {
				$checked = ' checked';
			}

			echo '<p><input type="checkbox" name="status[]" value="' . $status . '"' . $checked . ' />' . $value . '</p>';
		}
	}

	public function wc_status_messages() {
		$statuses = wc_get_order_statuses();
		$settings = get_option( SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY );

		foreach ($statuses as $status => $value) {
			echo '<tr><th scope="row"><label for="' . $status . '">' . $value . ' message</label></th><td><textarea class="regular-text" rows="7" name="' . $status .'" id="' . $status . '">' . $settings[$status] . '</textarea></td></tr>';
		}
	}
}