<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\DataMapper\DataMapperInterface;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;

/**
 * Class Repository
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class Repository implements RepositoryInterface
{
	/** @var  string  The name (type) of the entity */
	private $entityName;

	/** @var DataMapperInterface */
	private $dataMapper;

	/** @var  array */
	private $restrictions = [];

	/**
	 * Constructor
	 *
	 * @param   string               $entityName The name (type) of the entity
	 * @param   DataMapperInterface  $dataMapper The builder
	 */
	public function __construct($entityName, DataMapperInterface $dataMapper)
	{
		$this->entityName = $entityName;
		$this->dataMapper = $dataMapper;
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
		return $this->dataMapper->getById($id);
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
		$finder = $this->dataMapper->findOne();

		foreach ($this->restrictions as $filter)
		{
			$finder = $finder->with($filter['field'], $filter['op'], $filter['value']);
		}

		return $finder;
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
		$finder = $this->dataMapper->findAll();

		foreach ($this->restrictions as $filter)
		{
			$finder = $finder->with($filter['field'], $filter['op'], $filter['value']);
		}

		return $finder;
	}

	/**
	 * Adds an entity to the repo
	 *
	 * @param   object $entity The entity to add
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be added
	 */
	public function add($entity)
	{
		if (empty($entity->id))
		{
			$this->dataMapper->insert($entity);
		}
		else
		{
			$this->dataMapper->update($entity);
		}
	}

	/**
	 * Deletes an entity from the repo
	 *
	 * @param   object $entity The entity to delete
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be deleted
	 */
	public function remove($entity)
	{
		$this->dataMapper->delete($entity);
	}

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit()
	{
		$this->dataMapper->commit();
	}

	/**
	 * Define a condition.
	 *
	 * @param   mixed  $lValue The left value for the comparision
	 * @param   string $op     The comparision operator, one of the \Joomla\ORM\Finder\Operator constants
	 * @param   mixed  $rValue The right value for the comparision
	 *
	 * @return  void
	 */
	public function restrictTo($lValue, $op, $rValue)
	{
		$this->restrictions[] = [
			'field' => $lValue,
			'op'    => $op,
			'value' => $rValue
		];
	}
}
