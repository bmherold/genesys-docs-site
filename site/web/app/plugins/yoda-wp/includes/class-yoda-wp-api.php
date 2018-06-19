<?php

/**
 * Callback function for all API requests.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * This class defines all the callback functions for the API Routes.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Brian Herold <bmherold@gmail.com>
 */
class Yoda_WP_API {

	/**
	 * The DB util class for any custom WP Queries.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Yoda_WP_API_DB    $db    The DB object.
	 */
	private $db;

	public function __construct() {

		$this->load_dependencies();
		$this->db = new Yoda_WP_API_DB();

	}

	private function load_dependencies() {

		/**
		 * The class that defines all the route callbacks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-api-db.php';
	}

	/**
	 * Echos back the value supplied to the test route
	 * @since    1.0.0
	 */
	public function get_test ( WP_REST_Request $request ) {
		$test_value = $request->get_param( 'value' );

		return [
			'test_value' => $test_value,
			'query_params' =>  $request->get_query_params()
		];
	}

	/**
	 * Returns all the posts in the database.
	 * @since    1.0.0
	 */
	public function get_posts ( WP_REST_Request $request ) {
		return $this->db->get_posts();
	}

	/**
	 * Returns all the guides in the database.
	 *
	 * @since    1.0.0
	 */
	public function get_guides ( WP_REST_Request $request ) {
		$query_params = $request->get_query_params();

		$use_dummy_data = isset($query_params['dummy']) ? (bool)$query_params['dummy'] : false;
		return $this->db->get_guides('/', [], [], $use_dummy_data);
	}

}