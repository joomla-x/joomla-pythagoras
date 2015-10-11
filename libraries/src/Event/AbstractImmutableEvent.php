<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Event;

defined('JPATH_PLATFORM') or die;

use BadMethodCallException;

/**
 * This class implements the immutable base Event object used system-wide to offer orthogonality.
 *
 * @see    Joomla\Cms\Event\AbstractEvent
 * @since  4.0
 */
class AbstractImmutableEvent extends AbstractEvent
{
	/**
	 * A flag to see if the constructor has been
	 * already called.
	 *
	 * @var  boolean
	 */
	private $constructed = false;

	/**
	 * Constructor.
	 *
	 * @param   string  $name       The event name.
	 * @param   array   $arguments  The event arguments.
	 *
	 * @throws  BadMethodCallException
	 *
	 * @since   1.0
	 */
	public function __construct($name, array $arguments = array())
	{
		if ($this->constructed)
		{
			throw new BadMethodCallException(
				sprintf('Cannot reconstruct the AbstractImmutableEvent %s.', $this->name)
			);
		}

		$this->constructed = true;

		parent::__construct($name, $arguments);
	}

	/**
	 * Set the value of an event argument.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  BadMethodCallException
	 */
	public function offsetSet($name, $value)
	{
		throw new BadMethodCallException(
			sprintf(
				'Cannot set the argument %s of the immutable event %s.',
				$name,
				$this->name
			)
		);
	}

	/**
	 * Remove an event argument.
	 *
	 * @param   string  $name  The argument name.
	 *
	 * @return  void
	 *
	 * @throws  BadMethodCallException
	 *
	 * @since   1.0
	 */
	public function offsetUnset($name)
	{
		throw new BadMethodCallException(
			sprintf(
				'Cannot remove the argument %s of the immutable event %s.',
				$name,
				$this->name
			)
		);
	}
}
