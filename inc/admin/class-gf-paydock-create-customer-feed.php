<?php

GFForms::include_feed_addon_framework();

class GF_Paydock_Create_Customer_Feed extends GFFeedAddOn {

	protected $_version = GRAVITY_FORMS_PAYDOCK_VERSION;
	protected $_min_gravityforms_version = '1.9.16';
	protected $_slug = 'gfpaydock';
	protected $_path = 'gravityforms-paydock/inc/admin/class-gf-paydock-create-customer-feed.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms PayDock';
	protected $_short_title = 'PayDock Create Customer';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GF_Paydock_Feed
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GF_Paydock_Create_Customer_Feed();
		}

		return self::$_instance;
	}

	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 */
	public function init() {

		parent::init();
		add_filter( 'gform_confirmation', array( $this, 'update_confirmation_url' ), 10, 3 );
		add_filter( 'gform_settings_menu', array( $this, 'override_setting_tab_menu' ), 99 );


	}


	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Process the feed e.g. subscribe the user to a list.
	 *
	 * @param array   $feed  The feed object to be processed.
	 * @param array   $entry The entry object currently being processed.
	 * @param array   $form  The form object currently being processed.
	 *
	 * @return bool|void
	 */
	public function process_feed( $feed, $entry, $form ) {

		if ( !empty( $_POST['paydock_ref_id'] ) ) {

			$payment_source_token = $_POST['paydock_ref_id'];
			// Retrieve the name => value pairs for all fields mapped in the 'mappedFields' field map.
			$field_map = $this->get_field_map_fields( $feed, 'mappedFields' );

			// Loop through the fields from the field map setting building an array of values to be passed to the third-party service.
			$merge_vars = array();
			foreach ( $field_map as $name => $field_id ) {

				// Get the field value for the specified field id
				$merge_vars[ $name ] = $this->get_field_value( $form, $entry, $field_id );

			}

			$endpoint = '/customers';

			$data = array(
				'first_name' => $merge_vars['first_name'],
				'last_name'  => $merge_vars['last_name'],
				'email'      => $merge_vars['email'],
				'phone'      => $merge_vars['phone'],
				'token'      => $payment_source_token,
				'reference'  => (string) $entry['id']
			);

			$data = apply_filters( 'pd_create_customer_details', $data, $entry['id'] );

			$endpoint = apply_filters( 'pd_create_customer_endpoint', '/customers', $entry['id'] );

			// Rc_Cwh_Logger()->log( '==== Data to create new customer is ====', $data );
			// Rc_Cwh_Logger()->log( '==== Customer Endpoint is ====', $endpoint );
			$response = Gravity_Paydock()->make_request( 'POST', $endpoint, $data );
			//Rc_Cwh_Logger()->log( '==== Create Customer response ====', $response );
			if ( empty( $response->error ) ) {

				// Add note to Gravity Form Entry

				$data['customer_id'] = $customer_id = $response->resource->data->_id;
				$this->add_note( $entry['id'], 'Added Paydock Customer Id:'.$customer_id, 'success' );

				gform_update_meta( $entry['id'], 'paydock_customer_data', $data );
				gform_update_meta( $entry['id'], 'paydock_total', $merge_vars['total'] );

				do_action( 'pd_after_create_customer', $response, $customer_id, $entry['id'] );
			}


		}

	}

	public function update_confirmation_url( $confirmation, $form, $entry ) {
		$customer_data = gform_get_meta( $entry['id'], 'paydock_customer_data' );
		// customer id exist
		if ( !empty( $customer_data['customer_id'] ) ) {
			//append the entry id to url
			if ( !empty( $confirmation['redirect'] ) ) {
				$url = $confirmation['redirect'];
				//base64_encode()
				$url.= ( strpos( $url, '?' ) !== false ?"&":"?" ) . 'id='.urlencode( base64_encode( $entry['id'] ) );
				$url.= '&customerid='.urlencode( base64_encode( $customer_data['customer_id'] ) );
				//$url.= '&customer='.urlencode( $customer_data['customer_id']);
				$confirmation['redirect'] = $url;
			}
		}
		return $confirmation;
	}



	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------


	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		return array(
			array(
				'title'  => esc_html__( 'PayDock Settings', 'gfpaydock' ),

				'fields' => array(
					array(
						'name'    => 'paydock_secret_key',
						'tooltip' => esc_html__( 'Your PayDock API Secret Key', 'gfpaydock' ),
						'label'   => esc_html__( 'API Secret Key', 'gfpaydock' ),
						'type'    => 'paydock_secret_key_type',
						'class'   => 'medium',
					),
					array(
						'name'    => 'paydock_public_key',
						'tooltip' => esc_html__( 'Your PayDock API Public Key', 'gfpaydock' ),
						'label'   => esc_html__( 'API Public Key', 'gfpaydock' ),
						'type'    => 'paydock_public_key_type',
						'class'   => 'medium',
					),
					array(
						'name'    => 'paydock_api_mode',
						'label'   => esc_html__( 'API Mode', 'gfpaydock' ),
						'type'    => 'radio',
						'default_value' => 'Live',
						'choices' => array(
							array(
								'label' => esc_html__( 'Live', 'gfpaydock' ),

							),
							array(
								'label' => esc_html__( 'Sandbox', 'gfpaydock' ),

							),
						),
					),
				),
			),
		);
	}

	public function settings_paydock_public_key_type($field){
		$settings = $this->get_current_settings();
		$val = isset ($settings['paydock_public_key']) ? $settings['paydock_public_key']: '';
	 ?>
		<input type="password" name="_gaddon_setting_paydock_public_key" value="<?php echo $val; ?>" class="medium gaddon-setting gaddon-text" id="paydock_public_key">
	<?php }

	public function settings_paydock_secret_key_type(){
		$settings = $this->get_current_settings();
		$val = isset ($settings['paydock_secret_key']) ? $settings['paydock_secret_key']: '';
	 ?>
		<input type="password" name="_gaddon_setting_paydock_secret_key" value="<?php echo $val; ?>" class="medium gaddon-setting gaddon-text" id="paydock_secret_key">
	<?php }

	/**
	 * Configures the settings which should be rendered on the feed edit page in the Form Settings > PayDock area.
	 *
	 * @return array
	 */
	public function feed_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'PayDock Feed Settings', 'gfpaydock' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Feed name', 'gfpaydock' ),
						'type'    => 'text',
						'name'    => 'feedName',
						'class'   => 'small',
					),

					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Map Fields', 'gfpaydock' ),
						'type'      => 'field_map',
						'field_map' => array(

							array(
								'name'     => 'first_name',
								'label'    => esc_html__( 'First Name', 'gfpaydock' ),
								'required' => 0,
							),
							array(
								'name'       => 'last_name',
								'label'      => esc_html__( 'Last Name', 'gfpaydock' ),
								'required'   => 0,

							),
							array(
								'name'       => 'email',
								'label'      => esc_html__( 'Email', 'gfpaydock' ),
								'required'   => 0,
								'field_type' => array( 'email', 'hidden' ),
							),
							array(
								'name'       => 'phone',
								'label'      => esc_html__( 'Phone number', 'gfpaydock' ),
								'required'   => 0,

							),
							array(
								'name'       => 'total',
								'label'      => esc_html__( 'Total', 'gfpaydock' ),
								'required'   => 1,

							),


						),
					),
					array(
						'name'           => 'condition',
						'label'          => esc_html__( 'Condition', 'gfpaydock' ),
						'type'           => 'feed_condition',
						'checkbox_label' => esc_html__( 'Enable Condition', 'gfpaydock' ),
						'instructions'   => esc_html__( 'Process this simple feed if', 'gfpaydock' ),
					),
				),
			),
		);
	}

	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @return array
	 */
	public function feed_list_columns() {
		return array(
			'feedName'  => esc_html__( 'Name', 'gfpaydock' ),
		);
	}



	/**
	 * Prevent feeds being listed or created if an api key isn't valid.
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		// Get the plugin settings.
		$settings = $this->get_plugin_settings();

		// Access a specific setting e.g. an api key
		$key = rgar( $settings, 'apiKey' );

		return true;
	}
	public function plugin_settings_title() {
		return sprintf( esc_html__( "PayDock Settings", "gravityforms" ) );
	}
	function override_setting_tab_menu( $setting_tabs ) {
		foreach ( $setting_tabs as $key=>$setting ) {
			if ( $setting['name']=='gfpaydock' ) {
				$setting_tabs[$key]['label'] = 'PayDock';
			}
		}
		return $setting_tabs;
	}

}
