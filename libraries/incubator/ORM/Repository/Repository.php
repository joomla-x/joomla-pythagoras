<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Operator;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\Storage\EntityFinderInterface;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;
use Joomla\String\Normalise;

/**
 * Class Repository
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class Repository implements RepositoryInterface
{
	/** @var  string  The class of the entity */
	private $className;

	/** @var DataMapperInterface */
	private $dataMapper;

	/** @var  UnitOfWorkInterface */
	private $unitOfWork;

	/** @var  array */
	private $restrictions = [];

	/**
	 * Constructor
	 *
	 * @param   string               $className   The class of the entity
	 * @param   DataMapperInterface  $dataMapper  The builder
	 * @param   UnitOfWorkInterface  $unitOfWork  The UnitOfWork
	 */
	public function __construct($className, DataMapperInterface $dataMapper, UnitOfWorkInterface $unitOfWork)
	{
		$this->className  = $className;
		$this->dataMapper = $dataMapper;
		$this->unitOfWork = $unitOfWork;
		$this->unitOfWork->registerDataMapper($this->className, $this->dataMapper);
	}

	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->findOne()->with('id', \Joomla\ORM\Operator::EQUAL, $id)->getItem()
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
		$entity = $this->unitOfWork->getEntityRegistry()->getEntity($this->className, $id);

		if (empty($entity))
		{
			$entity = $this->findOne()->with('id', Operator::EQUAL, $id)->getItem();
		}

		return $entity;
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
		foreach ($this->restrictions as $preset)
		{
			if ($preset['op'] == Operator::EQUAL)
			{
				$property = Normalise::toVariable($preset['field']);
				$entity->$property = $preset['value'];
			}
		}

		$this->unitOfWork->scheduleForInsertion($entity);
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
		$this->unitOfWork->scheduleForDeletion($entity);
	}

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit()
	{
		$this->unitOfWork->commit();
	}

	/**
	 * Define a condition.
	 *
	 * @param   mixed  $lValue The left value for the comparision
	 * @param   string $op     The comparision operator, one of the \Joomla\ORM\Finder\Operator constants EQUAL or IN
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

	/**
	 * Gets the entity class managed with this repository
	 *
	 * @return string The entity class managed with this repository
	 */
	public function getEntityClass()
	{
		return $this->className;
	}
}
