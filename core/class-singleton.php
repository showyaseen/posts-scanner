<?php

/**
 * Singleton class for all classes.
 *
 *
 *
 * @author  Yaseen Taha (showyaseen@hotmail.com)
 * @package YTAHA\PostScanner
 *
 */

namespace YTAHA\PostScanner;

// Abort if called directly.
defined('WPINC') || die;

/**
 * Class Singleton
 *
 * @package YTAHA\PostScanner
 */
abstract class Singleton
{
	/**
	 * Singleton constructor.
	 *
	 * Protect the class from being initiated multiple times.
	 *
	 * @param array $props Optional properties array.
	 *
	 *
	 */
	protected function __construct($props = array())
	{
		// Protect class from being initiated multiple times.
	}

	/**
	 * Instance obtaining method.
	 *
	 * @return static Called class instance.
	 *
	 */
	public static function instance()
	{
		static $instances = array();

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		if (!isset($instances[$called_class_name])) {
			$instances[$called_class_name] = new $called_class_name();
		}

		return $instances[$called_class_name];
	}
}
