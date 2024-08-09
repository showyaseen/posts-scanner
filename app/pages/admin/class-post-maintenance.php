<?php

/**
 * Posts Maintenance Admin Page Class.
 *
 * @package   YTAHA\PostScanner
 * @category  Endpoint
 * @author    Yaseen Taha <showyaseen@hotmail.com>
 *
 */

namespace YTAHA\PostScanner\App\Pages\Admin;

// Abort if called directly.
defined('WPINC') || die;

class Post_Maintenance extends Abstract_Admin_Page
{

	/**
	 * Initializes the page.
	 *
	 * @return void
	 *
	 */
	public function init()
	{
		$this->page_title  = __('Post Maintenance', 'ytaha-posts-scanner');
		$this->page_slug   = 'ytaha_posts_scanner_post_maintenance';
		$this->unique_id   = "{$this->page_slug}-{$this->assets_version}";
		$this->assets_path = YTAHA_POSTS_SCANNER_DIR . 'assets/js/ytaha-posts-maintenance-page.min.asset.php';
	}

	public function register_admin_page()
	{
		$page = add_menu_page(
			'Posts Maintenance',
			$this->page_title,
			'manage_options',
			$this->page_slug,
			array($this, 'view'),
			'dashicons-admin-tools',
			6
		);

		add_action('load-' . $page, array($this, 'prepare_assets'));
	}

	/**
	 * Prepares assets.
	 *
	 * @return void
	 */
	public function prepare_assets()
	{
		if (!is_array($this->page_scripts)) {
			$this->page_scripts = array();
		}

		$handle       = 'ytaha_posts_scanner_post_maintenancepage';
		$src          = YTAHA_POSTS_SCANNER_ASSETS_URL . '/js/ytaha-posts-maintenance-page.min.js';
		$style_src    = YTAHA_POSTS_SCANNER_ASSETS_URL . '/css/ytaha-posts-maintenance-page.min.css';
		$dependencies = !empty($this->script_data('dependencies'))
			? $this->script_data('dependencies')
			: array(
				'react',
				'wp-element',
				'wp-i18n',
				'wp-is-shallow-equal',
				'wp-polyfill',
			);

		$this->page_scripts[$handle] = array(
			'src'         => $src,
			'style_src'   => $style_src,
			'deps'        => $dependencies,
			'ver'         => $this->assets_version,
			'strategy'    => true,
			'object_name' => 'ytahaPostScanner',
			'localize'    => array(
				'domElementId'          => $this->unique_id,
				'restEndpointPostsScan' => \YTAHA\PostScanner\Endpoints\V1\Posts_Scan::instance()->get_endpoint_url(),
				'restEndpointPostTypes' => rest_url('/wp/v2/types'),
			),
		);
	}
}
