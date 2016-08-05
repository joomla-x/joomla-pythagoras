<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Doctrine;

use Doctrine\DBAL\Connection;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Operator;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\Storage\EntityFinderInterface;

/**
 * Class DoctrineDataMapper
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class DoctrineDataMapper implements DataMapperInterface
{
	/** @var \Doctrine\DBAL\Connection The connection */
	private $connection;

	/** @var string  Class of the entity */
	private $entityClass;

	/** @var EntityBuilder The entity builder */
	private $builder;

	/** @var  string  Name of the data table */
	private $table;

	/** @var  EntityRegistry */
	private $entityRegistry;

	/**
	 * DoctrineDataMapper constructor.
	 *
	 * @param   Connection     $connection     The database connection
	 * @param   string         $entityClass    The class name of the entity
	 * @param   string         $table          The table name
	 * @param   EntityRegistry $entityRegistry The entity registry
	 */
	public function __construct(Connection $connection, $entityClass, $table, EntityRegistry $entityRegistry)
	{
		$this->connection     = $connection;
		$this->entityClass    = $entityClass;
		$this->table          = $table;
		$this->entityRegistry = $entityRegistry;
	}

	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->findOne()->with('id', \Joomla\ORM\Operator::EQUAL, '$id)->getItem()
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
		return new DoctrineEntityFinder($this->connection, $this->table, $this->entityClass, $this->entityRegistry);
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
		return new DoctrineCollectionFinder($this->connection, $this->table, $this->entityClass, $this->entityRegistry);
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
		$persistor = new DoctrinePersistor($this->connection, $this->table, $this->entityRegistry->getEntityBuilder(), $this->entityRegistry);
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
		$persistor = new DoctrinePersistor($this->connection, $this->table, $this->entityRegistry->getEntityBuilder(), $this->entityRegistry);
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
		$persistor = new DoctrinePersistor($this->connection, $this->table, $this->entityRegistry->getEntityBuilder(), $this->entityRegistry);
		$persistor->delete($entity);
	}
}
