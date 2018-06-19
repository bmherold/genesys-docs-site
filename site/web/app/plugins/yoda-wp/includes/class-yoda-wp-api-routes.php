<?php

/**
 * The API Class where callbacks are defined
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * This class defines all the API Routes.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author Brian Herold <bmherold@gmail.com>
 */
class Yoda_WP_API_Routes {

	/**
	 * The API object where all the callback functions are implemented.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Yoda_WP_API    $api    The API object.
	 */
	private $api;

	public function __construct() {

		$this->load_dependencies();
		$this->api = new Yoda_WP_API();

	}

	private function load_dependencies() {

		/**
		 * The class that defines all the route callbacks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-api.php';
	}

	/**
	 * Register the API endpoints for getting our custom data.
	 *
	 * @since    1.0.0
	 */
	public function rest_api_init () {
		// remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		// add_filter( 'rest_pre_serve_request', function( $value ) {
		// 	header( 'Access-Control-Allow-Origin: *' );
		// 	header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
		// 	header( 'Access-Control-Allow-Credentials: true' );
		// 	return $value;
		// });

		// remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		// add_filter( 'rest_pre_serve_request', function( $value ) {
		// 	$origin = get_http_origin();
		// 	if ( $origin && in_array( $origin, array(
		// 			'localhost:4400'
		// 		) ) ) {
		// 		header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $origin ) );
		// 		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
		// 		header( 'Access-Control-Allow-Credentials: true' );
		// 	}
		// 	return $value;
		// });

		// add_filter( 'allowed_http_origin', '__return_true' );

		// add_filter('http_origin', function() { return "http://localhost:4400";});

		$origin = 'https://localhost:4400';
		header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $origin ) );
		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
		header( 'Access-Control-Allow-Credentials: true' );

		register_rest_route('api/v1', '/test/(?P<value>.*)', array(
			'methods' => 'GET',
			'callback' => [$this->api, 'get_test']
		));

		register_rest_route('api/v1', '/posts', array(
			'methods' => 'GET',
			'callback' => [$this->api, 'get_posts']
		));

		register_rest_route('api/v1', '/guides', array(
			'methods' => 'POST',
			'callback' => [$this->api, 'get_guides']
		));
	}

}
