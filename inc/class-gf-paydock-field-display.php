<?php

if ( class_exists( 'GFForms' ) ) {


	class GF_PayDock_Field_Display extends GF_Field {

		public $type = 'paydock_field_display';

		public function get_form_editor_field_title() {
			return esc_attr__( 'Paydock Field Display', 'gfpaydock' );
		}

		function get_form_editor_field_settings() {
			return array();
		}

		public function get_field_input( $form, $value = '', $entry = null ) {
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$form_id  = absint( $form['id'] );
			$id       = absint( $this->id );
			$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
			$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;
			$class         = $this->size;


			if ( ! empty( $this->fields ) && is_array( $this->fields ) ) {

				$field_html = '';
				$settings = get_option( 'gravityformsaddon_gfpaydock_settings' );

				foreach ( $this->fields as $field ) {

					if ( $field->type == 'paydock_credit_card' ) {
						$width = $height = '400';
						if ( !empty( $field->config_token ) ) {
							$settings = get_option( 'gravityformsaddon_gfpaydock_settings' );
							$ref_id = mt_rand( 1000, 1000000000 );
							$url_params = $this->get_url_params( $field );
							$widget_url =  isset( $settings['paydock_api_mode'] ) && $settings['paydock_api_mode'] == 'Live' ? GF_PAYDOCK_WIDGET_API_LIVE_URL : GF_PAYDOCK_WIDGET_API_SANDBOX_URL;

							$url = $widget_url."/remote-action?public_key=".$settings['paydock_public_key']."&ref_id=".$ref_id.'&'.$url_params;

							if ( !empty( $field->paydock_cc_iframe_width ) ) {
								$width = $field->paydock_cc_iframe_width;
							}
							if ( !empty( $field->paydock_cc_iframe_height ) ) {
								$height = $field->paydock_cc_iframe_height;
							}

							$field_html .= "<div class='ginput_container ginput_container_email'>
				                            <iframe src='".$url."' width='".$width."' height='".$height."' ></iframe>
				                            <input type='hidden' name='paydock_ref_id' id='paydock_ref_id' value='' >
				                        </div>";
							// hide the submit button
							add_filter( 'gform_submit_button', '__return_false' );

						}

					} elseif ( $field->type == 'paydock_paypal' ) {
						$field_html .="Here goes paypal button";
					}
				}
			}


			return $field_html;

		}

		public function get_form_editor_button() {
			return array(
				'group' => 'paydock_fields_front',
				'text'  => $this->get_form_editor_field_title()
			);
		}

		public function get_url_params( $field ) {
			$url = $card = '';
			$url.='configuration_token='.$field->config_token.'&';

			$params = array(
				'paydock_cc_iframe_finish_text',
				'paydock_cc_iframe_font_size',
				'paydock_cc_iframe_fields_validation',
				'paydock_cc_iframe_background_color',
				'paydock_cc_iframe_text_color',
				'paydock_cc_iframe_border_color',
				'paydock_cc_iframe_button_color',
				'paydock_cc_iframe_error_color',
				'paydock_cc_iframe_success_color'
			);
			foreach ( $params as $param ) {
				if ( !empty( $field->{$param} ) ) {
					$param_name = str_replace( 'paydock_cc_iframe_', '', $param );
					$url.=$param_name. '=' .urlencode( $field->{$param} ) . '&';
				}
			}

			$card_types = array(
				'paydock_supported_ctype_visa'=>'visa',
				'paydock_supported_ctype_mastercard'=>'mastercard',
				'paydock_supported_ctype_american_express'=>'amex',
				'paydock_supported_ctype_diner_club_international'=>'diners',
				'paydock_supported_ctype_japanese_credit_bureau'=>'japcb',
				'paydock_supported_ctype_laser_deposits'=>'laser',
				'paydock_supported_ctype_solo'=>'solo'
			);
			foreach ( $card_types as $key => $card_type ) {
				if ( !empty( $field->{$key} ) ) {
					$card.= urlencode( $card_type ). ',';
				}
			}

			if ( !empty( $card ) ) {
				$url.='supported_card_types='.$card;
			}
			return $url;

		}

	}

	GF_Fields::register( new GF_PayDock_Field_Display() );
}
