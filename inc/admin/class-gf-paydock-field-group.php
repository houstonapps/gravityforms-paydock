<?php
class GF_Paydock_Field_Group {

	function __construct() {

		add_filter( 'gform_add_field_buttons', array( $this, 'add_paydock_group' ) );

		add_filter( 'gform_field_standard_settings', array( $this, 'add_configuration_token_box' ), 10, 2 );

		add_filter( 'gform_pre_render', array( $this, 'parse_form' ) );
	}

	public function add_paydock_group( $group ) {
		global $__gf_tooltips;
		$__gf_tooltips['form_paydock_fields']='<h6>' . __( 'PayDock Fields', 'gfpaydock' ) . '</h6>' . __( 'PayDock Fields allow you to add paydock payment fields to your form.', 'gfpaydock' );
		$__gf_tooltips['form_paydock_credit_card_config_token']='<h6>' . __( 'PayDock Fields', 'gfpaydock' ) . '</h6>' . __( 'Paste in configuration token here.', 'gfpaydock' );
		$group[]=array(
			'name' => 'paydock_fields',
			'label' => __( 'PayDock Fields', 'gfpaydock' ),
			'fields'=>array(
				array( 'class' => 'button', 'data-type' => 'paydock_credit_card', 'value' => GFCommon::get_field_type_title( 'paydock_credit_card' ) ),
				array( 'class' => 'button', 'data-type' => 'paydock_paypal', 'value' => GFCommon::get_field_type_title( 'paydock_paypal' ) )
			)
		);
		return $group;

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
	/*
	Parse form in frontend for paydock fields & move them to end of the form.
	 */
	function parse_form( $form ) {
		$paydock_field_exists = false;
		$paydock_display_field_properties = array( 'type' => 'paydock_field_display', 'fields'=>array() );

		foreach ( $form['fields'] as $key => $field ) {
			if ( $field instanceof GF_PayDock_Field_Credit_Card ) {
				$paydock_display_field_properties['fields'][] =$field;
				unset( $form['fields'][$key] );
				$paydock_field_exists= true;
			}
		}

		if ( $paydock_field_exists ) {
			$paydock_display_field  = GF_Fields::create( $paydock_display_field_properties );
			$form['fields'][]=$paydock_display_field;
		}

		return $form;
	}

}
new GF_Paydock_Field_Group();
