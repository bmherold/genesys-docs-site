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
		$defaults = array(
			'numberposts' => 5,
			'category' => 0, 'orderby' => 'date',
			'order' => 'DESC', 'include' => array(),
			'exclude' => array(), 'meta_key' => '',
			'meta_value' =>'', 'post_type' => 'post',
			'suppress_filters' => true
		);

		$r = wp_parse_args( $args, $defaults );
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
		return $get_posts->query($r);
	}

}
