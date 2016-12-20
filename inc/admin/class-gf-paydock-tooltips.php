<?php
class GF_Paydock_Tooltips {
	function __construct() {
		add_filter( 'gform_tooltips', array( $this, 'add_paydock_tooltips' ) );
	}

	public function add_paydock_tooltips( $tooltips ) {
		$tooltips['form_paydock_fields']='<h6>' . __( 'PayDock Fields', 'gfpaydock' ) . '</h6>' . __( 'PayDock Fields allow you to add paydock payment fields to your form.', 'gfpaydock' );
		$tooltips['form_paydock_credit_card_config_token']='<h6>' . __( 'PayDock Fields', 'gfpaydock' ) . '</h6>' . __( 'Paste in configuration token here.', 'gfpaydock' );

		$tooltips['paydock_supported_ctype']='<h6>' . __( 'Supported card types', 'gfpaydock' ) . '</h6>' . __( 'Define card types you want to show logo in your iFrame under Credit card name input. Available values: visa (VISA), mastercard (MASTERCARD), amex (American Express), diners (Diners Club International), japcb (Japanese Credit Bureau), laser (Laser Deposits), solo (Solo). Note: be sure, that gateway, you are using, accepts selected type of card.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_finish_text']='<h6>' . __( 'Finish Text', 'gfpaydock' ) . '</h6>' . __( 'Custom text after success finish of operation.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_font_size']='<h6>' . __( 'Font Size', 'gfpaydock' ) . '</h6>' . __( 'Size of labels, placeholders in iFrame. Use px or rem values.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_fields_validation']='<h6>' . __( 'Field Validation', 'gfpaydock' ) . '</h6>' . __( 'Parameter to turn on/off red borders of particular field, when getting error.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_background_color']='<h6>' . __( 'Background Color', 'gfpaydock' ) . '</h6>' . __( 'Color of a form background.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_text_color']='<h6>' . __( 'Text Color', 'gfpaydock' ) . '</h6>' . __( 'Color of a form text.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_border_color']='<h6>' . __( 'Border Color', 'gfpaydock' ) . '</h6>' . __( 'Color of all borders in a form.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_button_color']='<h6>' . __( 'Button Color', 'gfpaydock' ) . '</h6>' . __( 'Color of a button in a form', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_error_color']='<h6>' . __( 'Error Color', 'gfpaydock' ) . '</h6>' . __( 'Color of message text, when getting error.', 'gfpaydock' );
		$tooltips['paydock_cc_iframe_success_color']='<h6>' . __( 'Success Color', 'gfpaydock' ) . '</h6>' . __( 'Color of message text, when operation success.', 'gfpaydock' );

		return $tooltips;
	}
}

new GF_Paydock_Tooltips();
