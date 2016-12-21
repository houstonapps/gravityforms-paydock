<?php
if ( method_exists( 'GFForms', 'include_payment_addon_framework' ) ) {
	GFForms::include_payment_addon_framework();
	class GF_Paydock_Charge_Customer_Feed extends GFPaymentAddOn {
		protected $_version = "1.0";
		protected $_min_gravityforms_version = "1.8.12";
		protected $_slug = 'gfpaydockchargecustomer';
		protected $_path = 'gravityforms-paydock/inc/class-gf-paydock-charge-customer-feed.php';
		protected $_full_path = __FILE__;
		protected $_title = 'PayDock Charge Customer';
		protected $_short_title = 'PayDock Charge Customer';
		protected $_supports_callbacks = true;
		protected $_requires_credit_card = false;
		protected $is_payment_gateway = false;

		private static $_instance = null;
		/**
		 * Get an instance of this class.
		 *
		 * @return GF_Paydock_Charge_Customer_Feed
		 */
		public static function get_instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new GF_Paydock_Charge_Customer_Feed();
			}

			return self::$_instance;
		}

		/**
		 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
		 */
		public function init() {

			parent::init();



		}

		public function init_frontend() {

			parent::init_frontend();
			add_filter( 'gform_pre_render',  array( $this, 'parse_charge_form' ) );

			add_filter( 'gform_field_input', array( $this, 'add_hidden_field_to_charge_form' ), 10, 5 );
		}
		public function parse_charge_form( $form ) {

			if ( !empty( $_GET['id'] ) && !empty( $_GET['customerid'] ) ) {
				$customer_data = gform_get_meta( base64_decode( $_GET['id'] ), 'paydock_customer_data' );
				if ( !empty( $customer_data[ 'customer_id' ] ) && $customer_data[ 'customer_id' ] == base64_decode( $_GET['customerid'] ) ) {
					$info = !empty( $customer_data['first_name'] )? '<div>'.$customer_data['first_name'].' ':'';
					$info .= !empty( $customer_data['first_name'] )? $customer_data['last_name'].'</div>':'';
					$info .= !empty( $customer_data['first_name'] )? '<div>'.$customer_data['email'].'</div>':'';

					$section_break = array( 'type' => 'section', 'label'=>'Your Information' );
					$customer_properties = array( 'type' => 'html', 'content'=>$info );
					$customer_id_properties = array( 'type' => 'hidden', 'defaultValue'=>$_GET['customerid'], 'cssClass'=>'paydock-customer-id' );

					$section_field = GF_Fields::create( $section_break );
					$customer_field = GF_Fields::create( $customer_properties );
					$customer_id_hidden_field = GF_Fields::create( $customer_id_properties );
					array_unshift( $form['fields'], $section_field, $customer_field, $customer_id_hidden_field );
					//array_unshift($form['fields'],$section_field);
					return $form;
				}

			}
			return $form;

		}


		function add_hidden_field_to_charge_form( $input, $field, $value, $lead_id, $form_id ) {
			if ( $field->cssClass == 'paydock-customer-id' ) {
				$input = '<input name="paydock_customer_id" id="paydock_customer_id" type="hidden" class="gform_hidden" aria-invalid="false" value="'.$value.'">';
			}
			return $input;
		}

		public function feed_settings_fields() {

			return array(

				array(
					'description' => '',
					'fields'      => array(
						array(
							'name'     => 'feedName',
							'label'    => esc_html__( 'Name', 'gfpaydock' ),
							'type'     => 'text',
							'class'    => 'medium',
							'required' => true,
							'tooltip'  => '<h6>' . esc_html__( 'Name', 'gfpaydock' ) . '</h6>' . esc_html__( 'Enter a feed name to uniquely identify this setup.', 'gfpaydock' )
						),
						array(
							'name'     => 'transactionType',
							'label'    => esc_html__( 'Transaction Type', 'gfpaydock' ),
							'type'     => 'select',
							'onchange' => "jQuery(this).parents('form').submit();",
							'choices'  => array(
								array(
									'label' => esc_html__( 'Select a transaction type', 'gfpaydock' ),
									'value' => ''
								),
								array(
									'label' => esc_html__( 'One time Charge', 'gfpaydock' ),
									'value' => 'product'
								),
								array( 'label' => esc_html__( 'Subscription', 'gfpaydock' ), 'value' => 'subscription' ),
							),
							'tooltip'  => '<h6>' . esc_html__( 'Transaction Type', 'gfpaydock' ) . '</h6>' . esc_html__( 'Select a transaction type.', 'gfpaydock' )
						),
					)
				),
				array(
					'title'      => 'Subscription Settings',
					'dependency' => array(
						'field'  => 'transactionType',
						'values' => array( 'subscription' )
					),
					'fields'     => array(
						array(
							'name'     => 'recurringAmount',
							'label'    => esc_html__( 'Recurring Amount', 'gfpaydock' ),
							'type'     => 'select',
							'choices'  => $this->recurring_amount_choices(),
							'required' => true,
							'tooltip'  => '<h6>' . esc_html__( 'Recurring Amount', 'gfpaydock' ) . '</h6>' . esc_html__( "Select which field determines the recurring payment amount, or select 'Form Total' to use the total of all pricing fields as the recurring amount.", 'gfpaydock' )
						),
						array(
							'name'    => 'billingCycle',
							'label'   => esc_html__( 'Billing Cycle', 'gfpaydock' ),
							'type'    => 'billing_cycle',
							'tooltip' => '<h6>' . esc_html__( 'Billing Cycle', 'gfpaydock' ) . '</h6>' . esc_html__( 'Select your billing cycle.  This determines how often the recurring payment should occur.', 'gfpaydock' )
						),

						array(
							'name'     => 'transaction_end',
							'label'    => esc_html__( 'End', 'gfpaydock' ),
							'type'     => 'select',
							'onchange' => "showEndTransactionValueField(this)",
							'choices'  => array(
								array(
									'label' => esc_html__( 'Select when to end subscription', 'gfpaydock' ),
									'value' => ''
								),
								array(
									'label' => esc_html__( 'End Subscription After Amount', 'gfpaydock' ),
									'value' => 'end_amount_after'
								),
								array(
									'label' => esc_html__( 'End Subscription Before Amount', 'gfpaydock' ),
									'value' => 'end_amount_before'
								),
								array(
									'label' => esc_html__( 'End Subscription Total Amount', 'gfpaydock' ),
									'value' => 'end_amount_total'
								),

								array(
									'label' => esc_html__( 'End Subscription after total transactions count', 'gfpaydock' ),
									'value' => 'end_transactions'
								),
								array(
									'label' => esc_html__( 'Subscription End Date', 'gfpaydock' ),
									'value' => 'end_date'
								),

							),
							'tooltip'  => '<h6>' . esc_html__( 'Transaction End', 'gfpaydock' ) . '</h6>' . esc_html__( 'Select when to end transaction.', 'gfpaydock' ).'<h5>' . esc_html__( 'End Subscription Before Amount', 'gfpaydock' ) . '</h5>'
						),
						array(
							'name'     => 'transaction_end_value',
							'label'    => esc_html__( '', 'gfpaydock' ),
							'type'     => 'text',
							'class'    => 'small',
						),

						// array(
						//  'name'     => 'end_amount_before',
						//  'label'    => esc_html__( 'End Subscription Before Amount', 'gfpaydock' ),
						//  'type'     => 'text',
						//  'class'    => 'small',
						//  'tooltip'  => '<h6>' . esc_html__( 'End Subscription Before Amount', 'gfpaydock' ) . '</h6>' . esc_html__( 'Total amount of all success transactions (not to exceed).', 'gfpaydock' )
						// ),
						// array(
						//  'name'     => 'end_amount_total',
						//  'label'    => esc_html__( 'End Subscription Total Amount', 'gfpaydock' ),
						//  'type'     => 'text',
						//  'class'    => 'small',
						//  'tooltip'  => '<h6>' . esc_html__( 'End Subscription Total Amount', 'gfpaydock' ) . '</h6>' . esc_html__( 'Total amount to be payed with subscription (equals). NOTE: If last payment of subscription schedule will be less then $ 1.00, some of gateways could respond with error and subscription status will become <strong>failed</strong>.', 'gfpaydock' )
						// ),
						// array(
						//  'name'     => 'end_transactions',
						//  'label'    => esc_html__( 'End Subscription after total transactions count', 'gfpaydock' ),
						//  'type'     => 'text',
						//  'class'    => 'small',
						//  'tooltip'  => '<h6>' . esc_html__( 'End Subscription after total transactions count', 'gfpaydock' ) . '</h6>' . esc_html__( 'Total count of all success transactions.', 'gfpaydock' )
						// ),
						// array(
						//  'name'     => 'end_date',
						//  'label'    => esc_html__( 'Subscription End Date', 'gfpaydock' ),
						//  'type'     => 'text',
						//  'class'    => 'small datepicker ymd_dash',
						//  'tooltip'  => '<h6>' . esc_html__( 'Subscription End Date', 'gfpaydock' ) . '</h6>' . esc_html__( 'Date when subscription will end automatically. Date Format <strong>(YYYY-MM-DD)</strong>.', 'gfpaydock' )
						// ),


					)
				),
				array(
					'title'      => 'One time Charge',
					'dependency' => array(
						'field'  => 'transactionType',
						'values' => array( 'product', 'donation' )
					),
					'fields'     => array(
						array(
							'name'          => 'paymentAmount',
							'label'         => esc_html__( 'Payment Amount', 'gfpaydock' ),
							'type'          => 'select',
							'choices'       => $this->product_amount_choices(),
							'required'      => true,
							'default_value' => 'form_total',
							'tooltip'       => '<h6>' . esc_html__( 'Payment Amount', 'gfpaydock' ) . '</h6>' . esc_html__( "Select which field determines the payment amount, or select 'Form Total' to use the total of all pricing fields as the payment amount.", 'gfpaydock' )
						),
					)
				),
				array(
					'title'      => esc_html__( 'Other Settings', 'gfpaydock' ),
					'dependency' => array(
						'field'  => 'transactionType',
						'values' => array( 'subscription', 'product', 'donation' )
					),
					'fields'     => $this->other_settings_fields()
				),

			);
		}



		public function other_settings_fields() {
			$other_settings = array( array(
					'name'    => 'conditionalLogic',
					'label'   => esc_html__( 'Conditional Logic', 'gfpaydock' ),
					'type'    => 'feed_condition',
					'tooltip' => '<h6>' . esc_html__( 'Conditional Logic', 'gfpaydock' ) . '</h6>' . esc_html__( 'When conditions are enabled, form submissions will only be sent to the payment gateway when the conditions are met. When disabled, all form submissions will be sent to the payment gateway.', 'gfpaydock' )
				) );

			return $other_settings;
		}

		/* [
	 *  'is_authorized' => true|false,
	 *  'error_message' => 'Error message',
	 *  'transaction_id' => 'XXX',
	 *
	 *  //If the payment is captured in this method, return a 'captured_payment' array with the following information about the payment
	 *  'captured_payment' => ['is_success'=>true|false, 'error_message' => 'error message', 'transaction_id' => 'xxx', 'amount' => 20]
	 * ]
	 */

		public function authorize( $feed, $submission_data, $form, $entry ) {
			$error ='';
			if ( !empty( $_POST['paydock_customer_id'] ) ) {

				$data = array(
					"amount"=>$submission_data['payment_amount'],
					"currency"=>$entry['currency'],
					//"reference"=> $entry['id'],
					"description"=> "Charge using Form ".$form['id'],
					"customer_id"=>base64_decode( $_POST['paydock_customer_id'] )
				);
				$response = Gravity_Paydock()->make_request( 'POST', '/charges', $data );
				// echo '<pre>';
				// print_r( $response ); die;

				if ( empty( $response->error ) ) {
					$transaction_id = $response->resource->data->transactions[0]->_id;
					$result=  array(
						'is_authorized' => true,
						'error_message' => '',
						'transaction_id' => $transaction_id,
						'captured_payment'=>array(
							'is_success' => true,
							'amount' =>$response->resource->data->amount,
							'transaction_id' => $transaction_id,
							'error_message' => '',
						)
					);
				}else {
					if ( is_object( $response->error ) ) {
						$error = $response->error->message;
					}
					$result=  array(
						'is_authorized' => false,
						'error_message' => $error,
					);
				}
			}
			return $result;
		}

		//

		public function subscribe( $feed, $submission_data, $form, $entry ) {
			$error ='';
			if ( !empty( $_POST['paydock_customer_id'] ) ) {


				$data = array(
					'amount'=>$submission_data['payment_amount'],
					'currency'=>$entry['currency'],
					//'reference'=> $entry['id'],
					'description'=> 'Charge using Form '.$form['id'],
					'customer_id'=>base64_decode( $_POST['paydock_customer_id'] ),
					'schedule'=>array(
						'frequency'=>$feed['meta']['billingCycle_length'],
						'interval'=>$feed['meta']['billingCycle_unit'],
					),
				);
				if ( !empty( $feed['meta']['transaction_end'] ) ) {
					$data['schedule'][$feed['meta']['transaction_end']] =$feed['meta']['transaction_end_value'];
				}


				$response = Gravity_Paydock()->make_request( 'POST', '/subscriptions', $data );

				if ( empty( $response->error ) ) {
					$transaction_id = $response->resource->data->transactions[0]->_id;
					$result=  array(
						'is_success' => true,
						'error_message' => '',
						'subscription_id' => $transaction_id,
						'amount' =>$response->resource->data->amount

					);
				} else {
					if ( is_object( $response->error ) ) {
						$error = $response->error->message;

						if ( isset( $response->error->details ) ) {
							$error .= ': '.$response->error->details[0];
						}
					}
					/**
					 * TODO : Show Error on Gravity Form Frontend
					 *
					 */
					$result=  array(
						'is_authorized' => false,
						'error_message' => $error,
					);

				}
			}
			return $result;
		}
	}
}
