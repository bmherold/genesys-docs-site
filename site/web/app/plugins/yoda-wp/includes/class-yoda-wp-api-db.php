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
		return $this->queryPosts();
	}

	public function get_guides($route, $permissions, $users, $use_dummy_data = false) {
		if ($use_dummy_data) {
			return $this->getDummyGuideData();
		}

		return $this->getAnnouncements();
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

	private function getAnnouncements() {
		$announcements =  $this->queryPosts([
			'post_type' => 'announcement',
			'post_status' => 'publish',
		], true);

		// return $announcements;

		return $this->filterPosts($announcements);
	}

	private function filterPosts($posts) {
		error_log(print_r($posts,true));

		return array_map(function($x) {
			$x = $x->to_array();
			return [
				'title' => $x['post_title'],
				'steps' => [
					'selector' => current($x['meta']['announcement-url']),
					'content' => $x['post_content'],
				],
			];
		}, $posts);
	}

	private function queryPosts($options = [], $with_meta = false) {
		$defaults = [
			'numberposts' => 5,
			'category' => 0,
			'orderby' => 'date',
			'order' => 'DESC',
			'include' => array(),
			'exclude' => array(),
			'meta_key' => '',
			'meta_value' =>'',
			'post_type' => 'post',
			'suppress_filters' => true
		];
		$defaults = array_merge($defaults, $options);

		$r = wp_parse_args( [], $defaults );
		if ( empty( $r['post_status'] ) )
				$r['post_status'] = ( 'attachment' == $r['post_type'] ) ? 'inherit' : 'publish';
		if ( ! empty($r['numberposts']) && empty($r['posts_per_page']) )
				$r['posts_per_page'] = $r['numberposts'];
		if ( ! empty($r['category']) )
				$r['cat'] = $r['category'];
		if ( ! empty($r['include']) ) {
				$incposts = wp_parse_id_list( $r['include'] );
				$r['posts_per_page'] = count($incposts);  // only the number of posts included
				$r['post__in'] = $incposts;
		} elseif ( ! empty($r['exclude']) )
				$r['post__not_in'] = wp_parse_id_list( $r['exclude'] );

		$r['ignore_sticky_posts'] = true;
		$r['no_found_rows'] = true;

		$get_posts = new WP_Query;
		$post_results = $get_posts->query($r);

		if ($with_meta) {
			foreach ($post_results as &$post) {
				$post->meta = get_post_meta($post->ID);
			}
		}

		return $post_results;
	}

}
