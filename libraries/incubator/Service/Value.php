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
 * Value object trait.
 *
 * Adds the notion of equality to immutable objects.
 * Implemented as an abstract class here because traits are PHP 5.4 minimum.
 *
 * @package  Joomla/Service
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class Value extends Immutable
{
	/**
	 * Check for equality of this value against another value.
	 *
	 * Note: This is a generic test for equality and doesn't cover everything.
	 *       For example, $other may have additional properties and this would
	 *       still return true.  If this is important then override this method.
	 *
	 * @param   Value  $other  Another value object to compare with this one.
	 *
	 * @return  boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function equals(Value $other)
	{
		// Two value objects are considered equal if all their public properties have the same value.
		foreach ($this->getProperties() as $key => $value)
		{
			if (!$this->equalsRecursive($value, $other->{$key}))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Check for equality recursively.
	 *
	 * @param   mixed  $thing1  A thing to compare for equality against $thing2.
	 * @param   mixed  $thing2  A thing to compare for equality against $thing1.
	 *
	 * @return  boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private function equalsRecursive($thing1, $thing2)
	{
		// Types must match.
		if (gettype($thing1) != gettype($thing2))
		{
			return false;
		}

		// Values must match.
		switch (gettype($thing1))
		{
			case 'array':
				return $this->equalsArrays($thing1, $thing2);

			case 'object':
				return $this->equalsObjects($thing1, $thing2);

			default:
				if ($thing1 != $thing2)
				{
					return false;
				}
		}

		return true;
	}

	/**
	 * Checks arrays for equality recursively.
	 *
	 * @param   array  $thing1  An array to compare for equality against $thing2.
	 * @param   array  $thing2  An array to compare for equality against $thing1.
	 *
	 * @return  boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private function equalsArrays($thing1, $thing2)
	{
		// Arrays must have the same keys and values.
		foreach ($thing1 as $key => $value)
		{
			if (!$this->equalsRecursive($value, $thing2[$key]))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Check objects for equality recursively.
	 *
	 * @param   object  $thing1  An object to compare for equality against $thing2.
	 * @param   object  $thing2  An object to compare for equality against $thing1.
	 *
	 * @return  boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private function equalsObjects($thing1, $thing2)
	{
		// Value objects must be equal.
		if ($thing1 instanceof Value)
		{
			if (!($thing2 instanceof Value) || !$thing1->equals($thing2))
			{
				return false;
			}

			return true;
		}

		// Non-Value objects must have the same class.
		if (get_class($thing1) != get_class($thing2))
		{
			return false;
		}

		// Non-Value objects must have the same properties with the same values.
		foreach ($thing1 as $key => $value)
		{
			if (!$this->equalsRecursive($value, $thing2->{$key}))
			{
				return false;
			}
		}

		return true;
	}
}
