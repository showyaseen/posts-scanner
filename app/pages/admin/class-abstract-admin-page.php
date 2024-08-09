<?php

/**
 * Admin Page Abstract Class.
 *
 * @package   YTAHA\PostScanner
 * @category  Endpoint
 * @author    Yaseen Taha <showyaseen@hotmail.com>
 *
 */

namespace YTAHA\PostScanner\App\Pages\Admin;

// Abort if called directly.
defined('WPINC') || die;

use YTAHA\PostScanner\Base;

/**
 * Abstract class for Admin Pages.
 *
 * @package  YTAHA\PostScanner\App\Pages\Admin
 * @category Admin
 *
 */
abstract class Abstract_Admin_Page extends Base
{

	/**
	 * The page title.
	 *
	 * @var string
	 */
	private $page_title;

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * Page Assets.
	 *
	 * @var array
	 */
	protected $page_scripts = array();

	/**
	 * Assets version.
	 *
	 * @var string
	 */
	protected $assets_version = '';

	/**
	 * A unique string ID to be used in markup and JSX.
	 *
	 * @var string
	 */
	protected $unique_id = '';

	/**
	 * Scripts Assets.
	 *
	 * @var string
	 */
	protected $assets_path;

	/**
	 * Construct admin page abstract.
	 *
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->assets_version = !empty($this->script_data('version')) ? $this->script_data('version') : YTAHA_POSTS_SCANNER_VERSION;

		add_action('admin_menu', array($this, 'register_admin_page'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
		add_filter('admin_body_class', array($this, 'admin_body_classes'));
	}

	/**
	 * Gets assets data for the given key.
	 *
	 *
	 * @param  string $key The key for the script data.
	 * @return string|array The script data.
	 */
	protected function script_data(string $key = '')
	{
		$raw_script_data = $this->raw_script_data();

		return !empty($key) && !empty($raw_script_data[$key]) ? $raw_script_data[$key] : '';
	}

	/**
	 * Gets the script data from the assets PHP file.
	 *
	 *
	 * @return array The raw script data.
	 */
	protected function raw_script_data(): array
	{
		static $script_data = null;

		if (is_null($script_data) && file_exists($this->assets_path)) {
			$script_data = include $this->assets_path;
		}

		return (array) $script_data;
	}

	/**
	 * Prepares assets.
	 *
	 *
	 * @return void
	 */
	public function enqueue_assets()
	{
		if (!empty($this->page_scripts)) {
			foreach ($this->page_scripts as $handle => $page_script) {
				wp_register_script(
					$handle,
					$page_script['src'],
					$page_script['deps'],
					$page_script['ver'],
					$page_script['strategy']
				);

				if (!empty($page_script['localize']) && !empty($page_script['object_name'])) {
					wp_localize_script($handle, $page_script['object_name'], $page_script['localize']);
				}

				wp_enqueue_script($handle);

				if (!empty($page_script['style_src'])) {
					wp_enqueue_style($handle, $page_script['style_src'], array(), $this->assets_version);
				}
			}
		}
	}

	/**
	 * Adds the SUI class on markup body.
	 *
	 *
	 * @param  string $classes The existing body classes.
	 * @return string The modified body classes.
	 */
	public function admin_body_classes($classes = '')
	{
		if (!function_exists('get_current_screen')) {
			return $classes;
		}

		$current_screen = get_current_screen();

		if (empty($current_screen->id) || !strpos($current_screen->id, strval($this->page_slug))) {
			return $classes;
		}

		$classes .= ' sui-' . str_replace('.', '-', '2.12.23') . ' ';

		return $classes;
	}

	/**
	 * Prints the wrapper element which React will use as root.
	 *
	 * @return void
	 */
	public function view()
	{
		echo '<div id="' . esc_attr($this->unique_id) . '" class="sui-wrap"></div>';
	}

	/**
	 * Prepares assets.
	 *
	 *
	 * @return void
	 */
	public function prepare_assets()
	{
	}
}
