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
		$post_data = $request->get_json_params();
		$DEFAULT_LOCALE = 'en';

		$route = (isset($post_data['route']) && $post_data['route']) ? $post_data['route'] : false;
		$user_id = (isset($post_data['user_id']) && $post_data['user_id']) ? $post_data['user_id'] : false;
		$locale = (isset($post_data['locale']) && $post_data['locale']) ? $post_data['locale'] : $DEFAULT_LOCALE;

		// error_log("USER ID: {$post_data['user_id']}");

		$use_dummy_data = isset($query_params['dummy']) ? $query_params['dummy'] : false;
		return $this->db->get_guides($route, [], $user_id, $locale, $use_dummy_data);
	}

	/**
	 * Updates a particular guide with metadata.
	 *
	 * @since    1.0.0
	 */
	public function update_guide ( WP_REST_Request $request ) {
		$guide_id = $request->get_param( 'id' );
		$post_data = $request->get_json_params();

		if (!isset($post_data['user_id']) || !$post_data['user_id']) {
			return new WP_Error('yoda_wp__error_missing_user_id',__('Missing user_id'), ['status' => 400]);
		}

		$user_id = $post_data['user_id'];
		$guide = $this->db->getGuide($guide_id);

		if ($guide) {
			return $guide_result = $this->db->markGuideComplete($guide_id, $user_id);
		} else {
			return new WP_Error('yoda_wp__error_guide_not_found',__('A guide with this id does not exist'), ['status' => 400]);
		}

	}

}