<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Csv;

use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Storage\EntityFinderInterface;

/**
 * Class CsvEntityFinder
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class CsvEntityFinder extends CsvCollectionFinder implements EntityFinderInterface
{

	/**
	 * Fetch the entity
	 *
	 * @return  object
	 *
	 * @throws  EntityNotFoundException  if the specified entity does not exist.
	 */
	public function getItem()
	{
		
		$items = $this->getItems();

		if (empty($items))
		{
			throw new EntityNotFoundException;
		}

		return $items[0];
	}
}
