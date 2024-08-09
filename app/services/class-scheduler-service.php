<?php

/**
 * Scheduler Service Class.
 *
 * @package   YTAHA\PostScanner
 * @category  Endpoint
 * @version   1.0.1
 */

namespace YTAHA\PostScanner\App\Services;

// Abort if called directly.
defined('WPINC') || die;

use YTAHA\PostScanner\Base;

/**
 * Class Scheduler_Service
 *
 * Handles scheduling of events.
 *
 * @package YTAHA\PostScanner\App\Services
 *
 */
class Scheduler_Service extends Base
{

	/**
	 * Start time for schedule.
	 *
	 * @var string
	 */
	private $start_at;

	/**
	 * Schedule repeat interval.
	 *
	 * @var string
	 */
	private $schedule_repeat;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$schedule_settings = get_option(YTAHA_SCHEDULER_ID, array());

		if (!empty($schedule_settings['hook_handler'])) {
			add_action(YTAHA_SCHEDULER_ID, $schedule_settings['hook_handler']);
		}
	}

	/**
	 * Initialize the scheduler service.
	 *
	 * @param string $start_at        Start time for the schedule.
	 * @param string $schedule_repeat Optional. Schedule repeat interval. Default 'daily'.
	 *
	 * @return $this
	 */
	public function init($start_at = null, $schedule_repeat = 'daily')
	{
		$this->start_at        = $start_at ?? time();
		$this->schedule_repeat = $schedule_repeat;
		return $this;
	}

	/**
	 * Schedule hook handler.
	 *
	 * @param array $arguments Arguments for the schedule hook.
	 *
	 * @return bool|WP_Error
	 */
	public function schedule_hook($arguments)
	{
		$new_schedule_settings = array_merge($arguments, array('start_at' => $this->start_at));

		$last_schedule_settings = get_option(YTAHA_SCHEDULER_ID, array());

		if (!empty($last_schedule_settings)) {
			$cleared = wp_unschedule_hook(YTAHA_SCHEDULER_ID, true);
			if (false === $cleared || is_wp_error($cleared)) {
				return $cleared;
			}
			update_option(YTAHA_SCHEDULER_ID, $new_schedule_settings);
		} else {
			add_option(YTAHA_SCHEDULER_ID, $new_schedule_settings);
		}

		if (!wp_next_scheduled(YTAHA_SCHEDULER_ID)) {
			return wp_schedule_event(
				$new_schedule_settings['start_at'],
				$this->schedule_repeat,
				YTAHA_SCHEDULER_ID,
				array($new_schedule_settings),
				true
			);
		}
	}
}
