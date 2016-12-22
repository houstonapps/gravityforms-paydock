<?php
if ( class_exists( 'GFForms' ) ) {

	class GF_PayDock_Field_Credit_Card extends GF_Field {

		public $type = 'paydock_credit_card';

		public function get_form_editor_field_title() {
			return esc_attr__( 'C/Card iFrame', 'gravityforms' );
		}

		function get_form_editor_field_settings() {
			return array(
				'tab_label',
				'config_token',
				'paydock_cc_iframe_width',
				'paydock_cc_iframe_height',
				'paydock_supported_ctype',
				'paydock_cc_iframe_finish_text',
				'paydock_cc_iframe_font_size',
				'paydock_cc_iframe_fields_validation',
				'paydock_cc_iframe_background_color',
				'paydock_cc_iframe_text_color',
				'paydock_cc_iframe_border_color',
				'paydock_cc_iframe_button_color',
				'paydock_cc_iframe_error_color',
				'paydock_cc_iframe_success_color',

			);
		}

		public function get_field_input( $form, $value = '', $entry = null ) {

			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$form_id  = absint( $form['id'] );
			$id       = absint( $this->id );
			$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
			$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;
			$class         = $this->size;

			return "<div class='ginput_container ginput_container'>
							<input name='input_{$id}' id='{$field_id}' type='text' value='' class='{$class}' disabled='disabled' />
						</div>";;
		}

		public function get_field_content( $value, $force_frontend_label, $form ) {
			$form_id         = $form['id'];
			$admin_buttons   = $this->get_admin_buttons();
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$is_admin        = $is_entry_detail || $is_form_editor;
			$field_label     = 'Purpose: Payment Source Capture';
			$field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
			$field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );
			return $field_content;
		}

		public function get_form_editor_inline_script_on_page_render() {
			$script = "
					jQuery(document).bind( 'gform_load_field_settings', function( event, field, form ) {
						//console.log(field);
						jQuery( '#tab_label' ).val( field.tab_label == undefined ? '' : field.tab_label );
						jQuery( '#config_token' ).val( field.config_token == undefined ? '' : field.config_token );
						jQuery( '#paydock_supported_ctype_visa' ).prop( 'checked',field.paydock_supported_ctype_visa == undefined ? false : field.paydock_supported_ctype_visa );
						jQuery( '#paydock_supported_ctype_mastercard' ).prop( 'checked',field.paydock_supported_ctype_mastercard == undefined ? false : field.paydock_supported_ctype_mastercard );
						jQuery( '#paydock_supported_ctype_american_express' ).prop( 'checked',field.paydock_supported_ctype_american_express == undefined ? false : field.paydock_supported_ctype_american_express );
						jQuery( '#paydock_supported_ctype_diner_club_international' ).prop( 'checked',field.paydock_supported_ctype_diner_club_international == undefined ? false : field.paydock_supported_ctype_diner_club_international );
						jQuery( '#paydock_supported_ctype_japanese_credit_bureau' ).prop( 'checked',field.paydock_supported_ctype_japanese_credit_bureau == undefined ? false : field.paydock_supported_ctype_japanese_credit_bureau );
						jQuery( '#paydock_supported_ctype_laser_deposits' ).prop( 'checked',field.paydock_supported_ctype_laser_deposits == undefined ? false : field.paydock_supported_ctype_laser_deposits );
						jQuery( '#paydock_supported_ctype_solo' ).prop( 'checked',field.paydock_supported_ctype_solo == undefined ? false : field.paydock_supported_ctype_solo );

						jQuery('#paydock_cc_iframe_width').val( field.paydock_cc_iframe_width == undefined ? '' : field.paydock_cc_iframe_width );
						jQuery('#paydock_cc_iframe_height').val( field.paydock_cc_iframe_height == undefined ? '' : field.paydock_cc_iframe_height );
						jQuery('#paydock_cc_iframe_finish_text').val( field.paydock_cc_iframe_finish_text == undefined ? '' : field.paydock_cc_iframe_finish_text );
						jQuery('#paydock_cc_iframe_font_size').val( field.paydock_cc_iframe_font_size == undefined ? '' : field.paydock_cc_iframe_font_size );
						jQuery('#paydock_cc_iframe_fields_validation').prop( 'checked', field.paydock_cc_iframe_fields_validation == undefined ? false : field.paydock_cc_iframe_fields_validation );

						jQuery('#paydock_cc_iframe_background_color').iris('color', field.paydock_cc_iframe_background_color == undefined ? '' : field.paydock_cc_iframe_background_color );
						jQuery('#paydock_cc_iframe_text_color').iris('color',field.paydock_cc_iframe_text_color == undefined ? '' : field.paydock_cc_iframe_text_color );
						jQuery('#paydock_cc_iframe_border_color').iris('color', field.paydock_cc_iframe_border_color == undefined ? '' : field.paydock_cc_iframe_border_color );
						jQuery('#paydock_cc_iframe_button_color').iris('color', field.paydock_cc_iframe_button_color == undefined ? '' : field.paydock_cc_iframe_button_color );
						jQuery('#paydock_cc_iframe_error_color').iris('color', field.paydock_cc_iframe_error_color == undefined ? '' : field.paydock_cc_iframe_error_color );
						jQuery('#paydock_cc_iframe_success_color').iris('color', field.paydock_cc_iframe_success_color == undefined ? '' : field.paydock_cc_iframe_success_color );
					});";
			return $script;
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'paydock_fields',
				'text'  => $this->get_form_editor_field_title()
			);
		}
	}

	GF_Fields::register( new GF_PayDock_Field_Credit_Card() );
}
