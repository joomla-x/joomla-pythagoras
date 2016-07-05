<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage;

use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Persistor\PersistorInterface;

/**
 * Interface StorageProviderInterface
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
interface StorageProviderInterface
{
	/**
	 * Get an EntityFinder.
	 *
	 * @param   string $entityClass The name of the entity
	 *
	 * @return  EntityFinderInterface  The finder
	 */
	public function getEntityFinder($entityClass);

	/**
	 * Get a CollectionFinder.
	 *
	 * @param   string $entityClass The name of the entity
	 *
	 * @return  CollectionFinderInterface
	 */
	public function getCollectionFinder($entityClass);

	/**
	 * Get a Persistor.
	 *
	 * @param   string $entityClass The name of the entity
	 *
	 * @return  PersistorInterface
	 */
	public function getPersistor($entityClass);
}
