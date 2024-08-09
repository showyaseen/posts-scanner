<?php

/**
 * Posts Posts Scan Endpoint.
 *
 * @category  Endpoint
 * @package   YTAHA\PostScanner
 * @author    Yaseen Taha <showyaseen@hotmail.com>
 *
 */

namespace YTAHA\PostScanner\Endpoints\V1;

// Abort if called directly.
defined('WPINC') || die;

use YTAHA\PostScanner\Endpoint;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use YTAHA\PostScanner\App\Services\Posts_Scan_Service;
use YTAHA\PostScanner\App\Services\Scheduler_Service;

/**
 * Class Posts_Scan
 *
 * Handles the posts scan functionality.
 *
 * @category Endpoint
 * @package  YTAHA\PostScanner\Endpoints\V1
 * @author   Yaseen Taha <showyaseen@hotmail.com>
 *
 */
class Posts_Scan extends Endpoint
{
	/**
	 * API endpoint for the current endpoint.
	 *
	 *
	 * @var   string $endpoint
	 */
	protected $endpoint = 'posts/scan';

	/**
	 * Register the routes for handling auth functionality.
	 *
	 *
	 * @return void
	 */
	public function register_routes()
	{
		// Route to auth URL.
		register_rest_route(
			$this->get_namespace(),
			$this->get_endpoint(),
			array(
				array(
					'methods'             => 'POST',
					'args'                => array(
						'post_type' => array(
							'required'    => true,
							'description' => __(
								'Post type filter to customize the scan process.',
								'ytaha-posts-scanner'
							),
							'type'        => 'string',
						),
					),
					'callback'            => array($this, 'scanPosts'),
					'permission_callback' => array($this, 'edit_permission'),
				),
			)
		);

		add_filter('ytaha_posts_scanner_rest_settings_permission', array($this, 'scan_posts_permission'));
	}

	/**
	 * Permission callback to verify nonce and user capability.
	 *
	 *
	 * @param  WP_REST_Request $request The REST request.
	 * @return bool|WP_Error True if the request has access, WP_Error otherwise.
	 */
	public function scan_posts_permission($capable)
	{
		if (!$capable) {
			return new WP_Error(
				'rest_forbidden',
				__(
					'You do not have permissions to perform posts scan.',
					'ytaha-posts-scanner'
				),
				array('status' => 403)
			);
		}

		return $capable;
	}

	/**
	 * Callback function to scan posts.
	 *
	 *
	 * @param  WP_REST_Request $request The REST request.
	 * @return WP_REST_Response|WP_Error The response or WP_Error on failure.
	 */
	public function scanPosts($request)
	{
		$filters   = array();
		$post_type = sanitize_text_field($request['post_type']);
		if ('' !== $post_type) {
			$filters['post_type'] = $post_type;
		}

		$post_scan_service = Posts_Scan_Service::instance()->init($filters);
		$post_scan_service->start_posts_scan();

		// Schedule daily post scan starting from tomorrow.
		$schedule_start_at = strtotime('+1 day', time());
		$scheduler         = Scheduler_Service::instance()->init($schedule_start_at);
		$result            = $scheduler->schedule_hook(
			$post_scan_service->get_scan_arguments()
		);

		if (false === $result) {
			return new WP_Error(
				'error',
				__('Failed to create post scan schedule.', 'ytaha-posts-scanner'),
				array('status' => 500)
			);
		} elseif (is_wp_error($result)) {
			return $result;
		} else {
			return new WP_REST_Response(
				array(
					'status'  => 'success',
					'message' => __(
						'Scan completed successfully.',
						'ytaha-posts-scanner'
					),
				),
				200
			);
		}
	}
}
