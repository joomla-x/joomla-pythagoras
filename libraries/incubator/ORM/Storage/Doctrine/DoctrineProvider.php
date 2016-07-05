<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\ORM\Storage\Doctrine;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Persistor\PersistorInterface;
use Joomla\ORM\Storage\StorageProviderInterface;

/**
 * Class DoctrineProvider
 *
 * @package Joomla/ORM
 *
 * @since   1.0
 */
class DoctrineProvider implements StorageProviderInterface
{
	/** @var  string The dsn url */
	private $dsn;

	/** @var  string The table name */
	private $tableName;

	/** @var EntityBuilder */
	private $builder = null;

	/**
	 * DoctrineProvider constructor.
	 *
	 * @param   string        $dsn       The DSN describing the desired connection
	 * @param   EntityBuilder $builder   The entity builder
	 * @param   string        $tableName The name of the table
	 */
	public function __construct($dsn, EntityBuilder $builder, $tableName)
	{
		$this->dsn       = $dsn;
		$this->builder   = $builder;
		$this->tableName = $tableName;
	}

	/**
	 * Get an EntityFinder.
	 *
	 * @param   string $entityClass The name of the entity
	 *
	 * @return  EntityFinderInterface  The finder
	 */
	public function getEntityFinder($entityClass)
	{
		return new DoctrineEntityFinder($this->getConnection(), $this->tableName, $entityClass, $this->builder);
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
		return new DoctrineCollectionFinder($this->getConnection(), $this->tableName, $entityClass, $this->builder);
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
		return new DoctrinePersistor($this->getConnection(), $this->tableName);
	}

	/**
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	private function getConnection()
	{
		return DriverManager::getConnection(['url' => $this->dsn]);
	}
}
