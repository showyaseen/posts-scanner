<?php

/**
 * Class to offload plugin.
 *
 *
 *
 * @author  Yaseen Taha (showyaseen@hotmail.com)
 * @package YTAHA\PostScanner
 *
 */

namespace YTAHA\PostScanner;

use YTAHA\PostScanner\Base;

// If this file is called directly, abort.
defined('WPINC') || die;

final class Offload extends Base
{


	/**
	 * Cleaning functionality when deactivate the plugin.
	 *
	 *
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		$this->init();
	}

	/**
	 * initiate deactivate plugin functionalities
	 *
	 *
	 * @access private
	 * @return void
	 */
	private function init()
	{

		// Remove scheduled wp crons when plugin is deactivate
		wp_unschedule_hook(YTAHA_SCHEDULER_ID, true);
	}
}
