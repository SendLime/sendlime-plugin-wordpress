<?php

namespace SendLime\SendLime;

/**
 * Installer class
 */
class Installer {

	public function run() {
		$this->add_version();
		$this->set_wc_order_notification_settings();
	}

	public function add_version() {
		$installed = get_option( 'sendlime_installed' );

		if ( ! $installed ) {
			update_option( 'sendlime_installed', time() );
		}

		update_option( 'sendlime_version', SENDLIME_VERSION );
	}

	public function set_wc_order_notification_settings() {
		$settings = get_option( 'sendlime_wc_order_notification_settings' );

		if ( ! $settings ) {
			$defaultSettings = array(
				'enabled'                           => false,
				'from'                              => '',
				'api_key'                           => '',
				'api_secret'                        => '',
				'new_order_notification_enabled'    => false,
				'new_order_notification_message'    => 'Hi! A new order #{order_number} has been placed. Total: {order_amount} {order_currency}',
				'new_order_notification_phone'      => '',
				'debug_enabled'                     => false,
				'status'           => array(
					'wc-pending',
					'wc-processing',
					'wc-on-hold',
					'wc-completed',
					'wc-cancelled',
					'wc-refunded',
					'wc-failed'
				),
				'wc-pending'            => 'Hi {billing_first_name} {billing_last_name}! Your payment is pending for {shop_name} order #{order_number}.',
				'wc-processing'         => 'Hi {billing_first_name} {billing_last_name}! Your {shop_name} order #{order_number} is being processed.',
				'wc-on-hold'            => 'Hi {billing_first_name} {billing_last_name}! Your {shop_name} order #{order_number} is on-hold.',
				'wc-completed'          => 'Hi {billing_first_name} {billing_last_name}! Your {shop_name} order #{order_number} is delivered.',
				'wc-cancelled'          => 'Hi {billing_first_name} {billing_last_name}! Your {shop_name} order #{order_number} is cancelled.',
				'wc-refunded'           => 'Hi {billing_first_name} {billing_last_name}! Your {shop_name} order #{order_number} is refunded.',
				'wc-failed'             => 'Hi {billing_first_name} {billing_last_name}! Your {shop_name} order #{order_number} failed.',
			);

			update_option(SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY, $defaultSettings);
		}
	}
}