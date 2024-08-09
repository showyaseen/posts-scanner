<?php

/**
 * WP-CLI Command for Posts Scan.
 *
 * @package   YTAHA\PostScanner
 * @category  Endpoint
 * @author    Yaseen Taha <showyaseen@hotmail.com>
 *
 */

namespace YTAHA\PostScanner\App\CLI;

use WP_CLI;
use WP_CLI_Command;
use YTAHA\PostScanner\App\Services\Posts_Scan_Service;

/**
 * Class Posts_Scan_CLI_Command
 *
 * Handles the WP-CLI command for scanning posts.
 *
 * @package  YTAHA\PostScanner\CLI
 * @category CLI
 *
 */
class Posts_Scan_CLI_Command extends WP_CLI_Command
{
	/**
	 * Scans posts based on the provided post type filter.
	 *
	 * ## OPTIONS
	 *
	 * [--post_type=<post_type>]
	 * : The post type to filter by. If omitted, defaults to 'post'.
	 *
	 * ## EXAMPLE
	 *
	 *     wp posts_scanner scan --post_type=page
	 *
	 *
	 * @param  array $args       The command arguments.
	 * @param  array $assoc_args The associative arguments.
	 * @return void
	 */
	public function __invoke($args, $assoc_args)
	{
		$post_type = isset($assoc_args['post_type']) ? sanitize_text_field($assoc_args['post_type']) : '';

		WP_CLI::log("Starting post scan for post type: $post_type ...");

		try {

			$filters = [];
			if ('' !== $post_type) {
				$filters = array('post_type' => $post_type);
			}

			$service = Posts_Scan_Service::instance();
			$service->init($filters);
			$service->start_posts_scan();

			WP_CLI::success('Post scan completed successfully.');
		} catch (\Exception $e) {
			WP_CLI::error(sprintf('Error occurred during post scan: %s', $e->getMessage()));
		}
	}
}
