<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\DataMapper;

use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;

/**
 * Class DataMapper
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class DataMapper implements DataMapperInterface
{
	/** @var  string  The name (type) of the entity */
	private $entityClass;

	/** @var  object  Prebuilt (empty) entity */
	private $prototype = null;

	/** @var EntityBuilder */
	private $builder;

	/**
	 * Constructor
	 *
	 * @param   string        $entityClass The name (type) of the entity
	 * @param   EntityBuilder $builder     The builder
	 */
	public function __construct($entityClass, EntityBuilder $builder)
	{
		$this->entityClass = $entityClass;
		$this->builder     = $builder;
	}

	/**
	 * Build a prototype (once) for the entity.
	 *
	 * @return  void
	 */
	private function buildPrototype()
	{
		if (empty($this->prototype))
		{
			$this->prototype = $this->builder->create($this->entityClass);
		}
	}

	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->getOne()->with('id', \Joomla\ORM\Finder\Operator::EQUAL, '$id)->get()
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
		$this->buildPrototype();

		return $this->findOne()->with($this->prototype->key(), Operator::EQUAL, $id)->getItem();
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
		$this->buildPrototype();

		return $this->prototype->getStorage()->getEntityFinder($this->entityClass);
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
		$this->buildPrototype();

		return $this->prototype->getStorage()->getCollectionFinder($this->entityClass);
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
		throw new \LogicException(__METHOD__ . ' is not implemented.');
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
		throw new \LogicException(__METHOD__ . ' is not implemented.');
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
		throw new \LogicException(__METHOD__ . ' is not implemented.');
	}

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit()
	{
		throw new \LogicException(__METHOD__ . ' is not implemented.');
	}

	/**
	 * Get the meta data for the entity type
	 *
	 * @return mixed
	 */
	public function getMeta()
	{
		throw new \LogicException(__METHOD__ . ' is not implemented.');
	}
}
