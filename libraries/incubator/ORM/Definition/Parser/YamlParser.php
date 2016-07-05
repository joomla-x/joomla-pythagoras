<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

use Joomla\ORM\Definition\Locator\LocatorInterface;
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;

/**
 * Class YamlParser
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class YamlParser implements ParserInterface
{
	/**
	 * Open the description of the entity
	 *
	 * @param   string $descriptionFile The file with the entity description
	 *
	 * @return mixed
	 */
	public function open($descriptionFile)
	{
		throw new \LogicException(__METHOD__ . ' is not implemented.');
	}

	/**
	 * Parse the entity definition
	 *
	 * @param   Callable[]       $callbacks Hooks for pre- and postprocessing of elements
	 * @param   LocatorInterface $locator   The description file locator for related entities
	 *
	 * @return  EntityStructure
	 */
	public function parse($callbacks, LocatorInterface $locator)
	{
		$preset    = [];
		$structure = new EntityStructure($preset);

		return $structure;
	}
}
