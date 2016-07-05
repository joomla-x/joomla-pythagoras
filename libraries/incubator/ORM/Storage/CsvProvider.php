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
 * Class CsvProvider
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class CsvProvider implements StorageProviderInterface
{
	/** @var  string The name of the data file */
	private $dataFile;

	/**
	 * CsvProvider constructor.
	 *
	 * @param   string  $dataFile  The name of the data file
	 */
	public function __construct($dataFile)
	{
		$this->dataFile = $dataFile;
	}

	/**
	 * Get an EntityFinder.
	 *
	 * @param   string $entityClass T he name of the entity
	 *
	 * @return  EntityFinderInterface  The finder
	 */
	public function getEntityFinder($entityClass)
	{
		return new CsvModel($this->dataFile, $entityClass);
	}

	/**
	 * Get a CollectionFinder.
	 *
	 * @param   string $entityClass The name of the entity
	 *
	 * @return  CollectionFinderInterface
	 */
	public function getCollectionFinder($entityClass)
	{
		return new CsvModel($this->dataFile, $entityClass);
	}

	/**
	 * Get a Persistor.
	 *
	 * @param   string $entityClass The name of the entity
	 *
	 * @return  PersistorInterface
	 */
	public function getPersistor($entityClass)
	{
		return new CsvModel($this->dataFile, $entityClass);
	}
}
