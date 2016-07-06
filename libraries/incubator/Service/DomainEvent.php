<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

/**
 * Abstract base class for immutable domain events.
 *
 * Usage
 *   Domain Events are immutable objects that are completely defined by the arguments
 *   passed to them in their constructors.  Some basic checks are performed to
 *   try to enforce immutability, but these only really guard against accidental
 *   alteration of object state.
 *
 * @package  Joomla/Service
 *
 * @since    __DEPLOY_VERSION__
 */
abstract class DomainEvent extends Value
{
	/**
	 * Check for equality of this event against another event.
	 *
	 * This overrides the generic equality test because we want to
	 * include the requestedon timestamp too.
	 *
	 * @param   Value $other Another value object to compare with this one.
	 *
	 * @return  boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function equals(Value $other)
	{
		// Must have occurred at the same time.
		if ($this->requestedon != $other->requestedon)
		{
			return false;
		}

		return parent::equals($other);
	}
}
