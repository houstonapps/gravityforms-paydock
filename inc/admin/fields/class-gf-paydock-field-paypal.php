<?php
if ( class_exists( 'GFForms' ) ) {

	class GF_PayDock_Field_Paypal extends GF_Field {

		public $type = 'paydock_paypal';

		public function get_form_editor_field_title() {
			return esc_attr__( 'PayPal', 'gfpaydock' );
		}

		function get_form_editor_field_settings() {
			return array( 'tab_label' );
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'paydock_fields',
				'text'  => $this->get_form_editor_field_title()
			);
		}

		public function get_field_content( $value, $force_frontend_label, $form ) {
			$form_id         = $form['id'];
			$admin_buttons   = $this->get_admin_buttons();
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$is_admin        = $is_entry_detail || $is_form_editor;
			$field_label     = 'PayDock PayPal';
			$field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
			$field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );
			return $field_content;
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

	}

	GF_Fields::register( new GF_PayDock_Field_Paypal() );
}
