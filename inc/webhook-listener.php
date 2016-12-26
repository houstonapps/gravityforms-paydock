<?php

add_action( 'init', 'gf_paydock_webhook_listener' );

function gf_paydock_webhook_listener() {
	// echo '<pre>';
	// $payment_data = json_decode( '{"data":{"payment_source":"c65a9384-ac34-44ba-a963-d497a720bce7","custom_reference":"923978053","configuration_token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwdXJwb3NlIjoicGF5bWVudF9zb3VyY2UiLCJwcmVkZWZpbmVkX2ZpZWxkcyI6eyJ0eXBlIjoiY2FyZCIsImdhdGV3YXlfaWQiOiI1ODQyN2ViYzFkM2IxMmYxN2UxOGRiNGIifSwid2ViaG9va19kZXN0aW5hdGlvbiI6Imh0dHA6Ly9yZXF1ZXN0Yi5pbi8xZHdiZ3VjMSIsInN1Y2Nlc3NfcmVkaXJlY3RfdXJsIjoiIiwiZXJyb3JfcmVkaXJlY3RfdXJsIjoiIiwiZGVmaW5lZF9mb3JtX2ZpZWxkcyI6W10sImxhYmVsIjoiIiwiaWF0IjoxNDgxMjcyMjk5fQ.qGogzAQ2z8Dqf84Dhnl919PhlBSoP1KPCEs_b2odmns"},"signature":"c6cb29c3ee8954b8caacebaed3046ed21917e4cd0900e03e2c226c0df38255cd"}' );
	// $payment_source = get_option( 'paydock_payment_source' );
	//  var_dump(( $payment_source )); die;
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
