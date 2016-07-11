<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Csv;

use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\InvalidOperatorException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\Storage\EntityFinderInterface;

/**
 * Class CsvDataMapper
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class CsvDataMapper implements DataMapperInterface
{
	/** @var CsvDataGateway */
	private $gateway;

	/** @var string  Class of the entity */
	private $entityClass;

	/** @var EntityBuilder The entity builder */
	private $builder;

	/** @var  string  Name of the data table */
	private $table;



	/** @var  array  Conditions */
	private $conditions = [];

	/** @var  array  Ordering instructions */
	private $ordering = [];

	/** @var array */
	private $columns = [];

	/**
	 * CsvDataMapper constructor.
	 *
	 * @param   CsvDataGateway $gateway     The data gateway
	 * @param   string         $entityClass The class name of the entity
	 * @param   EntityBuilder  $builder     The entity builder
	 * @param   string         $table       The table name
	 */
	public function __construct(CsvDataGateway $gateway, $entityClass, $builder, $table)
	{
		$this->gateway     = $gateway;
		$this->entityClass = $entityClass;
		$this->builder     = $builder;
		$this->table       = $table;
	}

	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->getOne()->with('id', \Joomla\ORM\Operator::EQUAL, '$id)->get()
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
		return $this->with('id', Operator::EQUAL, $id)->getItem();
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
		return new CsvEntityFinder($this->gateway, $this->table, $this->entityClass, $this->builder);
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
		return new CsvCollectionFinder($this->gateway, $this->table, $this->entityClass, $this->builder);
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
		$persistor = new CsvPersistor($this->gateway, $this->table, $this->builder);
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
		$persistor = new CsvPersistor($this->gateway, $this->table, $this->builder);
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
		$persistor = new CsvPersistor($this->gateway, $this->table, $this->builder);
		$persistor->delete($entity, $idAccessorRegistry);
	}
}
