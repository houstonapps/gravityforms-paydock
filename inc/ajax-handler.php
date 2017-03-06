<?php

add_action( 'wp_ajax_gf_paydock_save_paypal_checkout_token', 'gf_paydock_save_paypal_checkout_token' );
add_action( 'wp_ajax_nopriv_gf_paydock_save_paypal_checkout_token', 'gf_paydock_save_paypal_checkout_token' );

function gf_paydock_save_paypal_checkout_token() {
	$settings = get_option( 'gravityformsaddon_gfpaydock_settings' );
	$reference_id = $_POST['token'];

	$gateway = get_transient( $reference_id );
	if ( $gateway ) {
		delete_transient( $reference_id );
		$data = array(
			'type'=>'paypal',
			'gateway_id'=>$gateway->_id,
			'checkout_token'=>$gateway->token,
			'mode'=> $gateway->mode
		);
		$response = Gravity_Paydock()->make_request( 'POST', '/payment_sources/tokens?public_key='.$settings['paydock_public_key'], $data );

		if ( empty( $response->error ) ) {
			    /**
				 * TODO: Think of storing in transient or any other way to automatically delete
				 * @var [type]
				 */
			//$payment_source = get_option( 'paydock_payment_source' );
			//$payment_source[$reference_id] = $response->resource->data;
			//update_option( 'paydock_payment_source', $payment_source );
			//echo $reference_id;
			echo $response->resource->data;
			die;
		}
		//print_r($response);
		echo 'error';
		die;
	}
}
