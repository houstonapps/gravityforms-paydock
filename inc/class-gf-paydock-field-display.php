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
						if ( !empty( $field->config_token ) ) {
							$ref_id = mt_rand( 1000, 1000000000 );

							$widget_url =  isset( $settings['paydock_api_mode'] ) && $settings['paydock_api_mode'] == 'Live' ? GF_PAYDOCK_WIDGET_API_LIVE_URL : GF_PAYDOCK_WIDGET_API_SANDBOX_URL;

							$url = $widget_url."/remote-action?public_key=".$settings['paydock_public_key']."&configuration_token=".$field->config_token."&ref_id=".$ref_id;
							$field_html .= "<div class='ginput_container ginput_container_email'>
					                            <iframe src='".$url."' width='400' height='400' ></iframe>
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

	}

	GF_Fields::register( new GF_PayDock_Field_Display() );
}
