<?php

class GF_Paydock_Field_Settings {
	function __construct() {
		add_filter( 'gform_field_standard_settings', array( $this, 'add_configuration_token_box' ), 10, 2 );
		add_action( 'gform_field_advanced_settings', array( $this, 'credit_card_field_advanced_settings' ), 10, 2 );
	}

	public function add_configuration_token_box( $pos, $form_id ) {
		if ( $pos == 200 ) {
		?>
		<li class="config_token field_setting">
			<label for="field_config_token" class="section_label">
				<?php esc_html_e( 'Configuration Token', 'gfpaydock' ); ?>
				<?php gform_tooltip( 'form_paydock_credit_card_config_token' ) ?>
			</label>
			<textarea  id="config_token" class="fieldwidth-3 fieldheight-1  mt-position-right mt-prepopulate" onkeyup="SetFieldProperty('config_token', this.value);"></textarea>
			<p>Paste in configuration token here.</p>
		</li>

		<?php
		}
	}

	function credit_card_field_advanced_settings( $position, $form_id ) {

		//create settings on position 50 (right after Admin Label)
		if ( $position == 50 ) {
			?>

			<li class="paydock_cc_iframe_width field_setting">
					<label for="paydock_cc_iframe_width" class="section_label">
						<?php esc_html_e( 'Iframe Width', 'gfpaydock' ); ?>
						<?php //gform_tooltip( 'paydock_cc_iframe_text_color' ) ?>
					</label>
					<input placeholder="300" type="text" id="paydock_cc_iframe_width" onblur="SetFieldProperty('paydock_cc_iframe_width', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_height field_setting">
					<label for="paydock_cc_iframe_height" class="section_label">
						<?php esc_html_e( 'Iframe Height', 'gfpaydock' ); ?>
						<?php //gform_tooltip( 'paydock_cc_iframe_text_color' ) ?>
					</label>
					<input placeholder="300" type="text" id="paydock_cc_iframe_height" onblur="SetFieldProperty('paydock_cc_iframe_height', this.value);" size="30" />
				</li>

		<li class="paydock_supported_ctype field_setting">
			<label for="paydock_supported_ctype_visa" class="section_label">
				<?php _e( "Supported card types", "gfpaydock" ); ?>
				<?php gform_tooltip( "paydock_supported_ctype" ) ?>
			</label>
			<input type="checkbox" id="paydock_supported_ctype_visa" onclick="SetFieldProperty('paydock_supported_ctype_visa', this.checked);" /> VISA
			<input type="checkbox" id="paydock_supported_ctype_mastercard" onclick="SetFieldProperty('paydock_supported_ctype_mastercard', this.checked);" /> MASTERCARD
			<input type="checkbox" id="paydock_supported_ctype_american_express" onclick="SetFieldProperty('paydock_supported_ctype_american_express', this.checked);" /> American Express
			<input type="checkbox" id="paydock_supported_ctype_diner_club_international" onclick="SetFieldProperty('paydock_supported_ctype_diner_club_international', this.checked);" /> Diners Club International
			<input type="checkbox" id="paydock_supported_ctype_japanese_credit_bureau" onclick="SetFieldProperty('paydock_supported_ctype_japanese_credit_bureau', this.checked);" /> Japanese Credit Bureau
			<input type="checkbox" id="paydock_supported_ctype_laser_deposits" onclick="SetFieldProperty('paydock_supported_ctype_laser_deposits', this.checked);" /> Laser Deposits
			<input type="checkbox" id="paydock_supported_ctype_solo" onclick="SetFieldProperty('paydock_supported_ctype_solo', this.checked);" /> Solo

		</li>

				<li class="paydock_cc_iframe_finish_text field_setting">
					<label for="paydock_cc_iframe_finish_text" class="section_label">
						<?php esc_html_e( 'Finish Text', 'gravityforms' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_finish_text' ) ?>
					</label>
					<input type="text" id="paydock_cc_iframe_finish_text" onblur="SetFieldProperty('paydock_cc_iframe_finish_text', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_font_size field_setting">
					<label for="paydock_cc_iframe_font_size" class="section_label">
						<?php esc_html_e( 'Font Size', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_font_size' ) ?>
					</label>
					<input placeholder="14px" type="text"  id="paydock_cc_iframe_font_size" onblur="SetFieldProperty('paydock_cc_iframe_font_size', this.value);" size="30" />
				</li>



				<li class="paydock_cc_iframe_background_color field_setting">
					<label for="paydock_cc_iframe_background_color" class="section_label">
						<?php esc_html_e( 'Background Color', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_background_color' ) ?>
					</label>
					<input type="text" class="paydock-color-field" id="paydock_cc_iframe_background_color"  onblur="SetFieldProperty('paydock_cc_iframe_background_color', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_text_color field_setting">
					<label for="paydock_cc_iframe_text_color" class="section_label">
						<?php esc_html_e( 'Text Color', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_text_color' ) ?>
					</label>
					<input type="text" class="paydock-color-field" id="paydock_cc_iframe_text_color" onblur="SetFieldProperty('paydock_cc_iframe_text_color', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_border_color field_setting">
					<label for="paydock_cc_iframe" class="section_label">
						<?php esc_html_e( 'Border Color', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_border_color' ) ?>
					</label>
					<input type="text" class="paydock-color-field" id="paydock_cc_iframe_border_color" onblur="SetFieldProperty('paydock_cc_iframe_border_color', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_button_color field_setting">
					<label for="paydock_cc_iframe_button_color" class="section_label">
						<?php esc_html_e( 'Button Color', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_button_color' ) ?>
					</label>
					<input type="text" class="paydock-color-field" id="paydock_cc_iframe_button_color" onblur="SetFieldProperty('paydock_cc_iframe_button_color', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_error_color field_setting">
					<label for="paydock_cc_iframe_error_color" class="section_label">
						<?php esc_html_e( 'Error Color', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_error_color' ) ?>
					</label>
					<input type="text" class="paydock-color-field" id="paydock_cc_iframe_error_color" onblur="SetFieldProperty('paydock_cc_iframe_error_color', this.value);" size="30" />
				</li>
				<li class="paydock_cc_iframe_success_color field_setting">
					<label for="paydock_cc_iframe_success_color" class="section_label">
						<?php esc_html_e( 'Success Color', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_success_color' ) ?>
					</label>
					<input type="text" class="paydock-color-field" id="paydock_cc_iframe_success_color" onblur="SetFieldProperty('paydock_cc_iframe_success_color', this.value);" size="30" />
				</li>

					<li class="paydock_cc_iframe_fields_validation field_setting">
					<label for="paydock_cc_iframe_fields_validation" class="section_label">
						<?php esc_html_e( 'Enable Fields Validation', 'gfpaydock' ); ?>
						<?php gform_tooltip( 'paydock_cc_iframe_fields_validation' ) ?>
					</label>
					<input type="checkbox" id="paydock_cc_iframe_fields_validation" onclick="SetFieldProperty('paydock_cc_iframe_fields_validation', this.checked);"   />Enable Fields Validation


				</li>


		<?php
		}
	}

}
new GF_Paydock_Field_Settings();
