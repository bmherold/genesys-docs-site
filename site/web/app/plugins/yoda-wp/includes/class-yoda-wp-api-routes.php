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
