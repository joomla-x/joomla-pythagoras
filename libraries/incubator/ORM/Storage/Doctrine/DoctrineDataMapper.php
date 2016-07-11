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
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\Storage\EntityFinderInterface;

/**
 * Class DoctrineDataMapper
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
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

	/**
	 * DoctrineDataMapper constructor.
	 *
	 * @param   Connection    $connection  The database connection
	 * @param   string        $entityClass The class name of the entity
	 * @param   EntityBuilder $builder     The entity builder
	 * @param   string        $table       The table name
	 */
	public function __construct(Connection $connection, $entityClass, $builder, $table)
	{
		$this->connection  = $connection;
		$this->entityClass = $entityClass;
		$this->builder     = $builder;
		$this->table       = $table;
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
	 * @param   object             $entity The entity to insert
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be inserted
	 */
	public function insert($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$persistor = new DoctrinePersistor($this->connection, $this->table, $this->builder);
		$persistor->insert($entity, $idAccessorRegistry);
	}

	/**
	 * Updates an entity in the storage
	 *
	 * @param   object             $entity The entity to insert
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be updated
	 */
	public function update($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$persistor = new DoctrinePersistor($this->connection, $this->table, $this->builder);
		$persistor->update($entity, $idAccessorRegistry);
	}

	/**
	 * Deletes an entity from the storage
	 *
	 * @param   object             $entity The entity to delete
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be deleted
	 */
	public function delete($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$persistor = new DoctrinePersistor($this->connection, $this->table, $this->builder);
		$persistor->delete($entity, $idAccessorRegistry);
	}
}
