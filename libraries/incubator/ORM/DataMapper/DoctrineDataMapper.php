<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\DataMapper;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Storage\Doctrine\DoctrineCollectionFinder;
use Joomla\ORM\Storage\Doctrine\DoctrineEntityFinder;
use Joomla\ORM\Storage\Doctrine\DoctrinePersistor;

/**
 * Class DoctrineDataMapper
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class DoctrineDataMapper implements DataMapperInterface
{
	/** @var  string  Database information */
	private $dsn;

	/** @var  string  Name of the data table */
	private $table;

	/** @var  string  Name of the definition file */
	private $definitionFile;

	/** @var string  Class of the entity */
	private $entityClass;

	/** @var \Doctrine\DBAL\Connection The connection */
	private $connection;

	/** @var EntityBuilder The entity builder */
	private $builder;

	/**
	 * CsvDataMapper constructor.
	 *
	 * @param   string $entityClass    The class name of the entity
	 * @param   string $definitionFile The definition file name
	 * @param   string $dsn            The dsn for the storage
	 * @param   string $table          The table
	 */
	public function __construct($entityClass, $definitionFile, $dsn, $table)
	{
		$this->entityClass    = $entityClass;
		$this->definitionFile = $definitionFile;
		$this->dsn            = $dsn;
		$this->table          = $table;

		$this->connection = DriverManager::getConnection(['url' => $this->dsn], new Configuration);

		$strategy      = new RecursiveDirectoryStrategy(dirname($this->definitionFile));
		$locator       = new Locator([$strategy]);
		$this->builder = new EntityBuilder($locator);
	}

	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->findOne()->with('id', \Joomla\ORM\Finder\Operator::EQUAL, '$id)->getItem()
	 *
	 * @param   mixed $id The id value
	 *
	 * @return  object  The requested entity
	 *
	 * @throws  EntityNotFoundException  if the entity does not exist
	 * @throws  OrmException  if there was an error getting the entity
	 */
	public function getById($id)
	{
		return $this->findOne()->with('id', Operator::EQUAL, $id)->getItem();
	}

	/**
	 * Find a single entity.
	 *
	 * @return  EntityFinderInterface  The responsible Finder object
	 *
	 * @throws  OrmException  if there was an error getting the entity
	 */
	public function findOne()
	{
		return new DoctrineEntityFinder($this->connection, $this->table, $this->entityClass, $this->builder);
	}

	/**
	 * Find multiple entities.
	 *
	 * @return  CollectionFinderInterface  The responsible Finder object
	 *
	 * @throws  OrmException  if there was an error getting the entities
	 */
	public function findAll()
	{
		return new DoctrineCollectionFinder($this->connection, $this->table, $this->entityClass, $this->builder);
	}

	/**
	 * Inserts an entity to the storage
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be inserted
	 */
	public function insert($entity)
	{
		$persistor = new DoctrinePersistor($this->connection, $this->table);
		$persistor->insert($entity);
	}

	/**
	 * Updates an entity in the storage
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be updated
	 */
	public function update($entity)
	{
		$persistor = new DoctrinePersistor($this->connection, $this->table);
		$persistor->update($entity);
	}

	/**
	 * Deletes an entity from the storage
	 *
	 * @param   object $entity The entity to delete
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be deleted
	 */
	public function delete($entity)
	{
		$persistor = new DoctrinePersistor($this->connection, $this->table);
		$persistor->delete($entity);
	}

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit()
	{
	}
}
