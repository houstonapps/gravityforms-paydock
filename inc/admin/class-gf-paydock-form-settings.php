<?php

/*
Class Not used Anymore
 */

// class GF_Paydock_Form_Settings {

// 	function __construct() {
// 		add_filter( 'gform_form_settings', array( $this, 'add_form_settings' ) , 10, 2  );

// 		// Save the form setting.
// 		add_filter( 'gform_pre_form_settings_save', array( $this, 'save_form_settings' ) );

// 	}

// 	public function add_form_settings( $form_settings, $form  ) {
// 		$enable_paydock_charge = "";

// 		if ( rgar( $form, 'enable_paydock_charge' ) ) {
// 			$enable_paydock_charge = 'checked="checked"';
// 		}

// 		$html = '
// 			<tr>
// 				<th>
// 					' . __( "PayDock Charge Creation", "gfsendy" ) . '
// 				</th>
// 				<td>
// 					<input type="checkbox" id="enable_sendy" name="enable_paydock_charge" value="1" '.$enable_paydock_charge.'   />
// 					<label for="gform_require_login">' . __( "Enable PayDock Charge Creation using this form. ", "gravityforms" ) . '</label>
// 				</td>
// 			</tr>';

// 		$paydock_options = array( "enable_paydock_charge" => $html, );
// 		$form_settings['PayDock Options'] = $paydock_options;
// 		return $form_settings;
// 	}

// 	public function save_form_settings( $updated_form ) {
// 		$updated_form['enable_paydock_charge'] = rgpost( 'enable_paydock_charge' );

// 		return $updated_form;
// 	}
// }

// new GF_Paydock_Form_Settings();
