<?php

function sendlime_wc_update_order_notification_settings( $args = [] ) {
	update_option( SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY, $args );
}

function sendlime_send_sms( $args = [] ) {
	return wp_remote_post("https://brain.sendlime.com/sms", array(
		'method'      => 'POST',
		'body'        => array(
			'to'            => $args['to'],
			'text'          => $args['text'],
			'from'          => $args['from'],
		),
		'headers'			=> array(
			'Authorization' => 'Basic ' . base64_encode( $args['api_key'] . ':' . $args['api_secret'] ),
		),
	));
}

/***
 * Sends debug email of send sms API response
 *
 * @param $response array|WP_Error
 * @param $email string
 *
 * @return void
 */
function sendlime_send_debug_mail( $response, $email, $order_id, $from, $to, $text ) {
	$subject = 'WC SendLime SMS Debug for #' . $order_id;

	if ( is_wp_error( $response ) ) {
		wp_mail( $email, $subject, 'Error: Something went wrong on the SendLime server or WP.' );
	} else {
		$message = '<b>DEBUG REPORT</b>';
		$message .= '<br /><br />Order ID: #' . $order_id;
		if ( $from ) {
			$message .= '<br />From: ' . $from;
		}
		$message .= '<br />To: ' . $to;
		$message .= '<br />Message: ' . $text;
		$message .= '<br /><br />API response from the SendLime server:';
		$message .= '<br /><pre>' . json_encode( json_decode(  $response[ 'body' ] ), JSON_PRETTY_PRINT ) . '</pre>';
		$message .= '<br /><p>Regards,<br />The SendLime Team</p>';
		$headers = array('Content-Type: text/html; charset=UTF-8');

		wp_mail( $email, $subject, $message, $headers );
	}
}

/**
 * Processes customers phone number
 *
 * @param $phone_number
 *
 * @return string
 */
function sendlime_process_phone_number( $phone_number ) {
	if ( strpos( $phone_number, '+' ) == 0 ) {
		$phone_number = substr($phone_number, 1);
	}

	if ( strpos( $phone_number, '1' ) == 0 ) {
		$phone_number = '880' . $phone_number;
	}

	if ( strpos( $phone_number, '88' ) != 0 ) {
		$phone_number = '88' . $phone_number;
	}

	return $phone_number;
}

/**
 * Processes order message by replacing variables with data
 *
 * @param $message
 * @param $order_details WC_Order|bool|WC_Order_Refund
 *
 * @return string
 */
function sendlime_process_order_message( $message, $order_details ) {
	$possible_variables = array(
		'shop_name'             => get_bloginfo( 'name' ),
		'order_number'          => $order_details->get_order_number(),
		'order_status'          => $order_details->get_status(),
		'order_currency'        => $order_details->get_currency(),
		'order_amount'          => $order_details->get_total(),
		'order_discount'        => $order_details->get_discount_total(),
		'order_date'            => $order_details->get_date_created(),
		'billing_first_name'    => $order_details->get_billing_first_name(),
		'billing_last_name'     => $order_details->get_billing_last_name(),
		'billing_address_1'     => $order_details->get_billing_address_1(),
		'billing_address_2'     => $order_details->get_billing_address_2(),
		'billing_state'         => $order_details->get_billing_state(),
		'billing_city'          => $order_details->get_billing_city(),
		'billing_postcode'      => $order_details->get_billing_postcode(),
		'billing_country'       => $order_details->get_billing_country(),
		'billing_company'       => $order_details->get_billing_company(),
		'shipping_first_name'   => $order_details->get_shipping_first_name(),
		'shipping_last_name'    => $order_details->get_shipping_last_name(),
		'shipping_address_1'    => $order_details->get_shipping_address_1(),
		'shipping_address_2'    => $order_details->get_shipping_address_2(),
		'shipping_state'        => $order_details->get_shipping_state(),
		'shipping_city'         => $order_details->get_shipping_city(),
		'shipping_postcode'     => $order_details->get_shipping_postcode(),
		'shipping_country'      => $order_details->get_shipping_country(),
		'shipping_company'      => $order_details->get_shipping_company(),
		'payment_method'        => $order_details->get_payment_method(),
		'payment_method_title'  => $order_details->get_payment_method_title(),
	);

	preg_match_all( "/{(.*?)}/", $message, $message_variables );

	foreach ( $message_variables[0] as $variable ) {
		$variable = str_replace(['{', '}'], '', $variable);
		$variable = strtolower( $variable );

		if ( ! array_key_exists( $variable, $possible_variables ) ) continue;

		foreach ( $possible_variables as $var => $value ) {
			if ( $variable == $var ) $message = str_replace( "{" . $variable . "}", $value, $message );
		}
	}

	return $message;
}

/**
 * Santizes and escapes
 *
 * @param $string
 * @param $type
 *
 * @return string|void
 */
function esc_sanitize( $string, $type = 'text' ) {
	if ( $type == 'text' ) return esc_attr( sanitize_text_field( $string ) );
	return esc_textarea( sanitize_textarea_field( $string ) );
}
