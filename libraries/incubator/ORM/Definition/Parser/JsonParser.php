<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

use Joomla\ORM\Definition\Locator\LocatorInterface;

/**
 * Class JsonParser
 *
 * @package  Joomla/orm
 * @since    1.0
 */
class JsonParser
{
	/**
	 * Parse the entity XML.
	 *
	 * @param   Callable[]       $callbacks Hooks for pre- and postprocessing of elements
	 * @param   LocatorInterface $locator   The XML description file locator
	 *
	 * @return  Entity
	 */
	public function parse($callbacks, LocatorInterface $locator)
	{
		// @TODO: Implement parse() method
		$className = __NAMESPACE__ . '\\Element';

		return new $className;
	}
}
