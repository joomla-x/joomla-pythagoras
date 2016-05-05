<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\ORM\Storage\Doctrine;

use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Exception\EntityNotFoundException;

/**
 * Class DoctrineEntityFinder
 *
 * @package Joomla/ORM
 *
 * @since 1.0
 */
class DoctrineEntityFinder extends DoctrineCollectionFinder implements EntityFinderInterface
{

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Finder\EntityFinderInterface::getItem()
	 */
	public function getItem()
	{
		$entities = parent::getItems();

		if (!is_array($entities) || count($entities) < 1)
		{
			throw new EntityNotFoundException();
		}
		return $entities[0];
	}
}
