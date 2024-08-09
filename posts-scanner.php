<?php
/**
 * Plugin Name:       Posts Scanner
 * Description:       Posts Scanner Plugin provides a user-friendly admin interface and a CLI command for scanning posts and updating their meta information..
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           0.1.0
 * Author:            Yaseen Taha
 * Text Domain:       ytaha-posts-scanner
 *
 * @package            YTAHA\PostScanner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}


// Plugin version.
if ( ! defined( 'YTAHA_POSTS_SCANNER_VERSION' ) ) {
	define( 'YTAHA_POSTS_SCANNER_VERSION', '1.0.0' );
}

// Define YTAHA_POSTS_SCANNER_PLUGIN_FILE.
if ( ! defined( 'YTAHA_POSTS_SCANNER_PLUGIN_FILE' ) ) {
	define( 'YTAHA_POSTS_SCANNER_PLUGIN_FILE', __FILE__ );
}

// Plugin directory.
if ( ! defined( 'YTAHA_POSTS_SCANNER_DIR' ) ) {
	define( 'YTAHA_POSTS_SCANNER_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin url.
if ( ! defined( 'YTAHA_POSTS_SCANNER_URL' ) ) {
	define( 'YTAHA_POSTS_SCANNER_URL', plugin_dir_url( __FILE__ ) );
}

// Assets url.
if ( ! defined( 'YTAHA_POSTS_SCANNER_ASSETS_URL' ) ) {
	define( 'YTAHA_POSTS_SCANNER_ASSETS_URL', YTAHA_POSTS_SCANNER_URL . '/assets' );
}

// Posts Scanner Settings Option Name.
if ( ! defined( 'YTAHA_POSTS_SCANNER_OPTION_NAME' ) ) {
	define( 'YTAHA_POSTS_SCANNER_OPTION_NAME', 'ytaha_posts_scanner_settings' );
}

// Scheduler Service ID.
if ( ! defined( 'YTAHA_SCHEDULER_ID' ) ) {
	define( 'YTAHA_SCHEDULER_ID', 'ytaha_posts_scanner_scheduler' );
}


/**
 * POSTS_SCANNER class.
 */
class POSTS_SCANNER {

	/**
	 * Holds the class instance.
	 *
	 * @var POSTS_SCANNER $instance
	 */
	private static $instance = null;

	/**
	 * Return an instance of the class
	 *
	 * Return an instance of the POSTS_SCANNER Class.
	 *
	 * @return POSTS_SCANNER class instance.
	 *
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class initializer.
	 */
	public function load() {
		load_plugin_textdomain(
			'ytaha-posts-scanner',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);

		YTAHA\PostScanner\Loader::instance();
	}

	/**
	 * Function for plugin deactivation cleanning.
	 */
	public function offload() {
		YTAHA\PostScanner\Offload::instance();
	}
}

// Init the plugin and load the plugin instance for the first time.
add_action(
	'plugins_loaded',
	function () {
		POSTS_SCANNER::get_instance()->load();
	}
);

// Register deactivation hook to clean plugin actions/data from the site.
register_deactivation_hook(
	__FILE__,
	function() {
		POSTS_SCANNER::get_instance()->offload();
	}
);
