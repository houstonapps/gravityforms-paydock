<?php
/*
Plugin Name: Gravity Forms Paydock
Plugin URI: http://www.paydock.com
Description: Integrate Paydock payment gateway.
Version:1.0
Author: Paydock
*/

define( 'GF_PAYDOCK_DIR', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );
define( 'GF_PAYDOCK_URL', plugins_url() . '/' . basename( dirname( __FILE__ ) ) );

// == API URLS ==
define( 'GF_PAYDOCK_API_SANDBOX_URL', 'https://api-sandbox.paydock.com/v1' );
define( 'GF_PAYDOCK_API_LIVE_URL', 'https://api.paydock.com/v1' );

// == Widget API URLS ==
define( 'GF_PAYDOCK_WIDGET_API_SANDBOX_URL', 'https://widget-sandbox.paydock.com' );
define( 'GF_PAYDOCK_WIDGET_API_LIVE_URL', 'https://widget.paydock.com' );

define( 'GRAVITY_FORMS_PAYDOCK_VERSION', '1.0' );
add_action( 'gform_loaded', array( 'GF_Paydock_AddOn_Bootstrap', 'load' ), 5 );
class GF_Paydock_AddOn_Bootstrap {
	public static function load() {
		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}
		require_once GF_PAYDOCK_DIR.'/inc/admin/class-gf-paydock-create-customer-feed.php';
		GFAddOn::register( 'GF_Paydock_Create_Customer_Feed' );
		require_once GF_PAYDOCK_DIR.'/inc/class-gf-paydock-charge-customer-feed.php';
		GFAddOn::register( 'GF_Paydock_Charge_Customer_Feed' );

		// == Admin ==
		require_once GF_PAYDOCK_DIR.'/inc/admin/class-gf-paydock-field-group.php';

		require_once GF_PAYDOCK_DIR.'/inc/admin/class-gf-paydock-field-settings.php';
		require_once GF_PAYDOCK_DIR.'/inc/admin/class-gf-paydock-tooltips.php';
		require_once GF_PAYDOCK_DIR.'/inc/admin/fields/class-gf-paydock-field-credit-card.php';
		require_once GF_PAYDOCK_DIR.'/inc/admin/fields/class-gf-paydock-field-paypal.php';


		// == Frontend ==
		require_once GF_PAYDOCK_DIR.'/inc/class-gf-paydock-field-display.php';
		require_once GF_PAYDOCK_DIR.'/inc/webhook-listener.php';

	}
}


class Gravity_Paydock {

	/**
	 *
	 *
	 * @var WP_Paydock
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Plugin Directory
	 *
	 * @since 1.0
	 * @var string $dir
	 */
	public static $dir = '';



	/**
	 * Plugin URL
	 *
	 * @since 1.0
	 * @var string $url
	 */
	public static $url = '';

	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof Gravity_Paydock ) ) {
			self::$instance = new Gravity_Paydock();

			self::$dir = plugin_dir_path( __FILE__ );

			self::$url = plugin_dir_url( __FILE__ );

		}
		return self::$instance;
	}

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );

	}

	public function add_scripts() {
		wp_enqueue_script( 'paydock-frontend', self::$url.'/js/paydock-frontend.js', array( 'jquery' ), false, false );
	}

	public function add_admin_scripts() {
		wp_enqueue_style( 'paydock-admin', self::$url.'/css/paydock-admin.css' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'paydock-admin', self::$url.'/js/paydock-admin.js', array( 'wp-color-picker' ), false, true );
	}


	public function make_request( $method = 'GET', $uri = '', $params = array()  ) {
		$settings = get_option( 'gravityformsaddon_gfpaydock_settings' );
		$api_url =  isset( $settings['paydock_api_mode'] ) && $settings['paydock_api_mode'] == 'Live' ? GF_PAYDOCK_API_LIVE_URL : GF_PAYDOCK_API_SANDBOX_URL;

		// setup the request
		$url     = $api_url . $uri;

		$headers =  array( 'content-type'=>'application/json', 'x-user-secret-key'=>$settings['paydock_secret_key'] );

		// switch based on METHOD
		/***********
		// GET is for getting reecords
		// POST is for updating existing records
		// PUT is for create NEW records
		// DELETE is for remove records and requires an ID
		************/

		switch ( $method ) {
		case 'GET':
			$querystring =urldecode( http_build_query( $params ) );
			// append a query string to the url
			$url         = $url.'?'.$querystring;
			// unset params on GET
			$params      = false;
			break;
		}

		// make the request
		$req_args = array(
			'method'    => $method,
			'body'      => json_encode( $params ),
			'headers'   => $headers,
			'sslverify' => true  // set to true in live envrio
		);

		// make the remote request
		$result = wp_remote_request( $url, $req_args );

		// handle response
		if ( !is_wp_error( $result ) ) {
			//no error
			$response = json_decode( wp_remote_retrieve_body( $result ) );
			return $response;

		} else {
			// error
			return $result->get_error_message();
		}

	}


}

function Gravity_Paydock() {
	return Gravity_Paydock::instance();
}
Gravity_Paydock();
