<?php

add_action( 'init', 'gf_paydock_webhook_listener' );

function gf_paydock_webhook_listener() {

	$webhookJSON = file_get_contents( 'php://input' );

	if ( is_string( $webhookJSON ) & !empty( $webhookJSON ) ) {
		$payment_data = json_decode( $webhookJSON );
		if ( is_object( $payment_data ) ) {
			if ( !empty( $payment_data->data ) ) {
				/**
				 * TODO: Think of storing in transient or any other way to automatically delete
				 * @var [type]
				 */
				$payment_source = get_option( 'paydock_payment_source' );
				$payment_source[$payment_data->data->custom_reference] = $payment_data->data->payment_source;
				update_option( 'paydock_payment_source', $payment_source );
			}
		}
	}

}
