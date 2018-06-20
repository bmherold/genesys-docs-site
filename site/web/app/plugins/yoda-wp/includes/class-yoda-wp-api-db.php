<?php

/**
 * Abstractions for any necessary custom DB queries
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * This class defines all the custom DB query functions.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Brian Herold <bmherold@gmail.com>
 */
class Yoda_WP_API_DB {

	public function __construct() {
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-yoda-wp-admin.php';
	}

	public function get_posts() {
		return $this->queryPosts(['post_type' => ['wizard', 'post', 'page', 'announcement']], true);
	}

	public function get_guides($route, $permissions, $user_id, $use_dummy_data = false) {
		if ($use_dummy_data) {
			return $this->getDummyGuideData();
		}

		// $announcements = $this->getAnnouncements($route, $permissions, $users);
		// // return $announcements;

		// $wizards = $this->getWizards($route, $permissions, $users);
		// // return $wizards;

		// return array_merge($announcements, $wizards);

		$guides = $this->queryPosts([
			'post_type' => ['announcement', 'wizard'],
			'post_status' => 'publish',
			'orderby' => 'ID',
			'order' => 'ASC',
		], true);

		// foreach($guides as $g) {
		// 	error_log(print_r($g->meta, true));
		// }

		if ($user_id) {
			error_log('FILTERING Completed GUIDES');
			$guides = $this->filterCompleteGuides($guides, $user_id);
		} else {
			error_log('NOT ----- FILTERING Completed GUIDES');
		}

		return $this->filterPosts($guides);
	}

	private function filterCompleteGuides($guides, $user_id) {
		global $wpdb;
		$table_guides_completed = $wpdb->prefix . Yoda_WP_Admin::TABLE_GUIDES_COMPLETED;

		$guide_ids = $wpdb->get_col( $wpdb->prepare(
			"
			SELECT      guide_id
			FROM        $table_guides_completed
			WHERE       user_id = %s
			",
			$user_id
		) );

		error_log('COMPLETED GUIDE IDS: ' . print_r($guide_ids, true));

		$filtered_guides = array_filter($guides, function($guide) use ($guide_ids) {
			$is_show_once = $this->getGuideShowOnceValue($guide);
			if (!$is_show_once) {
				return true;
			} else {
				return !in_array($guide->ID, $guide_ids);
			}
		});

		$clean_array = array();
		foreach($filtered_guides as $x) {
			$clean_array[] = $x;
		}

		return $clean_array;
	}

	private function getGuideShowOnceValue($guide) {
		$guide = $guide->to_array();
		switch ($guide['post_type']) {
			case 'announcement':
				if (isset($guide['meta']['announcement-show-once'])) {
					return current($guide['meta']['announcement-show-once']);
				}
				return false;
				break;

			case 'wizard':
				if (isset($guide['meta']['wizard-show-once'])) {
					return current($guide['meta']['wizard-show-once']);
				}
				return false;
				break;

			default:
				return false;
				break;
		}
	}

	// private function getAnnouncements() {
	// 	$announcements =  $this->queryPosts([
	// 		'post_type' => 'announcement',
	// 		'post_status' => 'publish',
	// 	], true);

	// 	// return $announcements;

	// 	return $this->filterPosts($announcements);
	// }

	// private function getWizards() {
	// 	$wizards =  $this->queryPosts([
	// 		'post_type' => 'wizard',
	// 		'post_status' => 'publish',
	// 	], true);

	// 	// return $wizards;

	// 	return $this->filterPosts($wizards);
	// }

	public function getGuide($guide_id) {
		error_log('[finding guide ' . $guide_id.']');
		$guide = get_post($guide_id);
		error_log('-------------------------');
		error_log(print_r($guide, true));

		if ($guide) {
			return $guide;
		} else {
			return false;
		}
	}

	public function markGuideComplete($guide_id, $user_id) {
		error_log('user_id ' . $user_id);
		global $wpdb;

		$table_guides_completed = $wpdb->prefix . Yoda_WP_Admin::TABLE_GUIDES_COMPLETED;

		$record = $wpdb->get_row( "SELECT * FROM $table_guides_completed WHERE guide_id = $guide_id and user_id = '$user_id'", ARRAY_A );

		error_log(print_r($record, true));

		if ($record) {
			error_log('GUIDE ALREADY COMPLETE');
			return $record;
		} else {
			error_log("INSERTING GUIDE with $user_id");
			$wpdb->insert(
				$table_guides_completed,
				[
					'guide_id' => $guide_id,
					'user_id' => $user_id,
					'completed_on' => current_time( 'mysql' )
				],
				[	'%d',	'%s', '%s' ]
			);

			return $wpdb->get_row( "SELECT * FROM $table_guides_completed WHERE id = {$wpdb->insert_id}", ARRAY_A );
		}

	}



	private function filterPosts($posts) {
		// error_log(print_r($posts,true));

		return array_map(function($x) {
			$x = $x->to_array();

			switch ($x['post_type']) {
				case 'announcement':
					return [
						'id' => $x['ID'],
						'steps' => [[
							'title' => $x['post_title'],
							'selector' => isset($x['meta']['announcement-selector']) ? current($x['meta']['announcement-selector']) : '',
							'content' => isset($x['post_content']) ? $x['post_content'] : '',
							]],
							'type' => $x['post_type'],
							'created' => $x['post_date'],
						'updated' => $x['post_modified'],
					];

					break;

					case 'wizard':
						$steps = unserialize(current($x['meta']['wizard-steps-repeater']));
						return [
							'id' => $x['ID'],
							'title' => $x['post_title'],
							'steps' => array_map(function($s) {
								return [
									'title' => isset($s['step-title']) ? $s['step-title'] : '',
									'selector' => $s['step-selector'],
									'content' => isset($s['stepContent']) ? $s['stepContent'] : '',
								];
							}, $steps),
							'type' => $x['post_type'],
							'created' => $x['post_date'],
							'updated' => $x['post_modified'],
						];

						break;

				default:
					return $x;
					break;
			}
		}, $posts);
	}

	private function queryPosts($options = [], $with_meta = false) {
    // $defaults = array(
		// 	'numberposts' => 5,
		// 	'category' => 0,
		//  'orderby' => 'date',
		// 	'order' => 'DESC',
		//  'include' => array(),
		// 	'exclude' => array(),
		//  'meta_key' => '',
		// 	'meta_value' =>'',
		//  'post_type' => 'post',
		// 	'suppress_filters' => true
		// );
		$post_results = get_posts($options);

		if ($with_meta) {
			foreach ($post_results as &$post) {
				$post->meta = get_post_meta($post->ID);
			}
		}

		return $post_results;
	}

	private function getDummyGuideData() {
		return [
			[
				"title" => "titsle",
				"steps" => [
					[
						"selector" => '.section-header',
						"content"  => '<div> WIZARDS</div>'
					],
					[
						"selector" => '.breadcrumbs',
						"content"  => '<div> WIZARDS EVERYWHERE </div>'
					]
				]
			]
		];
	}

}
