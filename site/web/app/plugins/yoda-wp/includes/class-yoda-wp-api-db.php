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

	}

	public function get_posts() {
		return $this->queryPosts(['post_type' => ['wizard', 'post', 'page', 'announcement']], true);
	}

	public function get_guides($route, $permissions, $users, $use_dummy_data = false) {
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

		return $this->filterPosts($guides);
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
