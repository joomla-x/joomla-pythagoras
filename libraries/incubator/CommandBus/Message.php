<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CommandBus;

/**
 * Message interface.
 *
 * Adds the notion of a "raised on" time to an immutable value object.
 * Implemented as an abstract class here because traits are PHP 5.4 minimum.
 *
 * @package  Joomla/Service
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class Message extends Value
{
	/**
	 * Constructor.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		// Save time of object construction as a property.
		// Convert microtime to string to avoid loss of precision due to overflow.
		$parts = explode(' ', microtime());
		$this->raisedon = sprintf('%d%03d', $parts[1], $parts[0] * 1000);

		parent::__construct();
	}

	/**
	 * Get the properties of the object.
	 *
	 * @return  array of key-value pairs.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getProperties()
	{
		$properties = parent::getProperties();

		// Unset the properties we don't want to expose.
		unset($properties['raisedon']);

		return $properties;
	}

	/**
	 * Get the timestamp indicating when this message was raised.
	 *
	 * @return  string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getRaisedOn()
	{
		return $this->raisedon;
	}
}
