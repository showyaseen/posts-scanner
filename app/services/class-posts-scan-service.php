<?php

/**
 * Posts Scan Service Class.
 *
 * @package   YTAHA\PostScanner
 * @category  Endpoint
 * @author    Yaseen Taha <showyaseen@hotmail.com>
 *
 */

namespace YTAHA\PostScanner\App\Services;

// Abort if called directly.
defined('WPINC') || die;

use YTAHA\PostScanner\Base;

/**
 * Class Posts_Scan_Service
 *
 * Handles the scanning and updating of posts' metadata.
 *
 * @package YTAHA\PostScanner\App\Services
 *
 */
class Posts_Scan_Service extends Base
{

	/**
	 * Post meta key.
	 *
	 * @var string
	 */
	private $post_meta_key;

	/**
	 * Posts initial filters and arguments.
	 *
	 * @var array
	 */
	private $filters = array(
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * Initialize the service with optional filters and post meta key.
	 *
	 * @param array  $filters       Optional. Additional filters for fetching posts. Default empty array.
	 * @param string $post_meta_key Optional. Post meta key to be used. Default 'ytaha_test_last_scan'.
	 *
	 * @return $this
	 */
	public function init($filters = array(), $post_meta_key = 'ytaha_test_last_scan')
	{
		$this->filters       = array_merge($this->filters, $filters);
		$this->post_meta_key = $post_meta_key;
		return $this;
	}

	/**
	 * Get the arguments for scanning.
	 *
	 * @return array
	 */
	public function get_scan_arguments()
	{
		return array(
			'meta_key'     => $this->post_meta_key,
			'filters'      => $this->filters,
			'hook_handler' => array(self::instance(), 'perform_scan'),
		);
	}

	/**
	 * Start the posts scan.
	 *
	 * @return void
	 */
	public function start_posts_scan()
	{
		$this->perform_scan($this->get_scan_arguments());
	}

	/**
	 * Perform the posts scan.
	 *
	 * @param array $args Arguments for the scan.
	 *
	 * @return void
	 */
	public function perform_scan($args)
	{
		$post_ids = get_posts($args['filters']);

		$this->batch_update_post_meta(
			$post_ids,
			$args['meta_key'],
			current_time('mysql')
		);
	}

	/**
	 * Batch update post meta.
	 *
	 * For very large datasets, it might be necessary to batch the updates to avoid memory or execution time limits.
	 *
	 * @param array  $post_ids   Array of post IDs.
	 * @param string $meta_key   Meta key to update.
	 * @param string $meta_value Meta value to set.
	 * @param int    $batch_size Optional. Size of each batch. Default 1000.
	 *
	 * @return void
	 */
	private function batch_update_post_meta($post_ids, $meta_key, $meta_value, $batch_size = 1000)
	{
		$chunks = array_chunk($post_ids, $batch_size);

		foreach ($chunks as $chunk) {
			$this->update_post_meta_in_bulk($chunk, $meta_key, $meta_value);
		}
	}

	/**
	 * Update post meta in bulk.
	 *
	 * @param array  $post_ids   Array of post IDs.
	 * @param string $meta_key   Meta key to update.
	 * @param string $meta_value Meta value to set.
	 *
	 * @return void|false False if no posts to update.
	 */
	private function update_post_meta_in_bulk($post_ids, $meta_key, $meta_value)
	{
		global $wpdb;

		if (empty($post_ids)) {
			return false; // No posts to update.
		}

		// Convert post_ids to a comma-separated string for the IN clause.
		$post_ids_in = implode(',', array_map('intval', $post_ids));

		// Delete previous meta_key values from posts.
		$delete_sql = "
			DELETE FROM {$wpdb->postmeta}
			WHERE meta_key = %s
			AND post_id IN ($post_ids_in)
		";
		$wpdb->query($wpdb->prepare($delete_sql, $meta_key));

		// Prepare the new meta_key values for insertion.
		$insert_values = array();
		foreach ($post_ids as $post_id) {
			$insert_values[] = $wpdb->prepare('(%d, %s, %s)', $post_id, $meta_key, $meta_value);
		}
		$insert_values_sql = implode(', ', $insert_values);

		// Prepare and execute the insertion query.
		$insert_sql = "
			INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
			VALUES {$insert_values_sql}
		";
		$wpdb->query($insert_sql);
	}
}
