<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Definition\Parser\HasManyThrough;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Operator;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\EntityFinderInterface;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;

/**
 * Class Repository
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class MappingRepository implements RepositoryInterface
{
	/** @var  string  The class of the entity */
	private $className;

	/** @var  RepositoryInterface */
	private $entityRepository;

	/** @var  RepositoryInterface */
	private $mapRepository;

	/** @var  HasManyThrough */
	private $relation;

	/** @var  UnitOfWorkInterface */
	private $unitOfWork;

	/** @var  array */
	private $restrictions = [];

	/** @var  array */
	private $map;

	/**
	 * Constructor
	 *
	 * @param   RepositoryInterface $entityRepository The entity repository
	 * @param   RepositoryInterface $mapRepository    The mapping repository
	 * @param   HasManyThrough      $relation         The relation
	 * @param   UnitOfWorkInterface $unitOfWork       The unit of work
	 */
	public function __construct(RepositoryInterface $entityRepository, RepositoryInterface $mapRepository, HasManyThrough $relation, UnitOfWorkInterface $unitOfWork)
	{
		$this->entityRepository = $entityRepository;
		$this->mapRepository    = $mapRepository;
		$this->relation         = $relation;
		$this->className        = $entityRepository->getEntityClass();
		$this->unitOfWork       = $unitOfWork;
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
		$this->updateMap();

		if (!in_array($id, $this->map))
		{
			throw new EntityNotFoundException;
		}

		return $this->entityRepository->getById($id);
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
		$this->updateMap();

		return $this->entityRepository->findOne()->with('id', Operator::IN, $this->map);
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
		$this->updateMap();

		return $this->entityRepository->findAll()->with('id', Operator::IN, $this->map);
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
		$idAccessorRegistry = $this->unitOfWork->getEntityRegistry()->getIdAccessorRegistry();
		$entityId           = $idAccessorRegistry->getEntityId($entity);

		$mapClass = $this->mapRepository->getEntityClass();

		if (empty($entityId))
		{
			$this->entityRepository->add($entity);
			$map         = new $mapClass;
			$varJoinName = $this->relation->varJoinName();
			$this->mapRepository->add($map);
			$this->unitOfWork->getEntityRegistry()->registerAggregateRootCallback(
				$entity,
				$map,
				function ($root, $child) use ($varJoinName, $idAccessorRegistry) {
					$entityId              = $idAccessorRegistry->getEntityId($root);
					$child->{$varJoinName} = $entityId;
					$this->map[]           = $entityId;
				}
			);

			return;
		}

		if (in_array($entityId, $this->map))
		{
			throw new OrmException("Entity with id $entityId aleady exists");
		}

		$map                 = new $mapClass;
		$varJoinName         = $this->relation->varJoinName();
		$map->{$varJoinName} = $entityId;
		$this->mapRepository->add($map);
		$this->map[] = $entityId;
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
		$idAccessorRegistry = $this->unitOfWork->getEntityRegistry()->getIdAccessorRegistry();
		$entityId           = $idAccessorRegistry->getEntityId($entity);

		$this->updateMap();

		if (!in_array($entityId, $this->map))
		{
			throw new EntityNotFoundException;
		}

		$colJoinName = $this->relation->colJoinName();
		$mapObject   = $this->mapRepository->findOne()->with($colJoinName, Operator::EQUAL, $entityId)->getItem();
		$this->mapRepository->remove($mapObject);
		$this->map = array_diff($this->map, [$entityId]);
	}

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit()
	{
		$this->entityRepository->commit();
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

	/**
	 * Update the internal mapping
	 *
	 * @return  void
	 */
	private function updateMap()
	{
		$this->map = $this->mapRepository
			->findAll()
			->columns($this->relation->colJoinName())
			->getItems();
	}

	/**
	 * Find all entities.
	 *
	 * getAll() is a convenience method, It is equivalent to
	 * ->findAll()->getItems()
	 *
	 * @return  object[]  The requested entities
	 *
	 * @throws  OrmException  if there was an error getting the entities
	 */
	public function getAll()
	{
		return $this->findAll()->getItems();
	}

	/**
	 * Create a new entity
	 *
	 * @param   array $row A hash with the properties for the new entity
	 *
	 * @return  object
	 */
	public function createFromArray(array $row)
	{
		throw new \LogicException(__METHOD__ . ' is not implemented.');
	}
}
