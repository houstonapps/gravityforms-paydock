<?php
class GF_Paydock_Field_Group {

	function __construct() {

		add_filter( 'gform_add_field_buttons', array( $this, 'add_paydock_group' ) );
		add_filter( 'gform_pre_render', array( $this, 'parse_form' ) );
	}

	public function add_paydock_group( $group ) {

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


	/*
	Parse form in frontend for paydock fields & move them to end of the form.
	 */
	function parse_form( $form ) {
		$paydock_field_exists = false;
		$paydock_display_field_properties = array( 'type' => 'paydock_field_display', 'fields'=>array() );

		foreach ( $form['fields'] as $key => $field ) {
			if ( $field instanceof GF_PayDock_Field_Credit_Card || $field instanceof GF_PayDock_Field_Paypal ) {
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
