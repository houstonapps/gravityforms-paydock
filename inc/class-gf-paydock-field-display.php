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
				$number_of_tabs = 0;

				$tabs_heading = '<div class="gravity-forms-paydock-tabs-head">';
				$settings = get_option( 'gravityformsaddon_gfpaydock_settings' );

				foreach ( $this->fields as $field ) {

					if ( $field->type == 'paydock_credit_card' ) {
						$width = $height = '400';
						if ( !empty( $field->config_token ) ) {
							$ref_id = mt_rand( 1000, 1000000000 );

							$tabs_heading  .='<a href="javascript:void(0)" class="tab-'.$ref_id.'">'.$field->tab_label.'</a>';
							$settings = get_option( 'gravityformsaddon_gfpaydock_settings' );

							$url_params = $this->get_url_params( $field );
							$widget_url =  isset( $settings['paydock_api_mode'] ) && $settings['paydock_api_mode'] == 'Live' ? GF_PAYDOCK_WIDGET_API_LIVE_URL : GF_PAYDOCK_WIDGET_API_SANDBOX_URL;

							$url = $widget_url."/remote-action?public_key=".$settings['paydock_public_key']."&ref_id=".$ref_id.'&'.$url_params;

							if ( !empty( $field->paydock_cc_iframe_width ) ) {
								$width = $field->paydock_cc_iframe_width;
							}
							if ( !empty( $field->paydock_cc_iframe_height ) ) {
								$height = $field->paydock_cc_iframe_height;
							}

							$field_html .= '<div id="tab-'.$ref_id.'"  class ="tabs_container ginput_container">
				                            <iframe src="'.$url.'" width="'.$width.'" height="'.$height.'" ></iframe>
				                            <input type="hidden" name="paydock_ref_id" id="paydock_ref_id" value="" >
				                        </div>';
							// hide the submit button
							add_filter( 'gform_submit_button', '__return_false' );
							$number_of_tabs++;
						}

					} elseif ( $field->type == 'paydock_paypal' ) {
						$tab_id = mt_rand( 1000, 1000000000 );
						$tabs_heading  .='<a href="javascript:void(0)" class="tab-'.$tab_id.'">'.$field->tab_label.'</a>';

						$data = array(
							'mode'=>'test',
							'type'=>'paypal',
							'gateway_id'=>'585a5aa624a02f3029436761',
							'success_redirect_url'=>'http://paydock.dev/',
							'error_redirect_url'=>'http://paydock.dev/',
							'description'=> 'My test PayDock description'
						);
						$response = Gravity_Paydock()->make_request( 'POST', '/payment_sources/external_checkout', $data );
						// echo '<pre>';
						// var_dump($response);
						// echo '</pre>';
						$field_html .= '<div id="tab-'.$tab_id.'" class ="tabs_container ginput_container">';
						$field_html .="Here goes paypal button";
						$field_html .= '</div>';
						$number_of_tabs++;
					}
				}
			}

			if ( $number_of_tabs > 1 ) {
				return $tabs_heading.$field_html;
			}else {
				//add the class to show the single container
				$field_html = str_replace( 'tabs_container', 'tabs_container show_tab', $field_html );
			}
			return $field_html;

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

		public function get_form_inline_script_on_page_render( $form ) {
			return 'jQuery(".gravity-forms-paydock-tabs-head a").eq(0).trigger("click")';
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'paydock_fields_front',
				'text'  => $this->get_form_editor_field_title()
			);
		}

	}

	GF_Fields::register( new GF_PayDock_Field_Display() );
}
