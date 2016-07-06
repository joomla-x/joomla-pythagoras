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
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;

/**
 * Interface RepositoryInterface
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
interface RepositoryInterface
{
	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->getOne()->with('id', \Joomla\ORM\Finder\Operator::EQUAL, '$id)->get()
	 *
	 * @param   mixed  $id  The id value
	 *
	 * @return  object  The requested entity
	 *
	 * @throws  EntityNotFoundException  if the entity does not exist
	 * @throws  OrmException  if there was an error getting the entity
	 */
	public function getById($id);

	/**
	 * Find a single entity.
	 *
	 * @return  EntityFinderInterface  The responsible Finder object
	 *
	 * @throws  OrmException  if there was an error getting the entity
	 */
	public function findOne();

	/**
	 * Find multiple entities.
	 *
	 * @return  CollectionFinderInterface  The responsible Finder object
	 *
	 * @throws  OrmException  if there was an error getting the entities
	 */
	public function findAll();

	/**
	 * Adds an entity to the repo
	 *
	 * @param   object $entity The entity to add
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be added
	 */
	public function add($entity);

	/**
	 * Deletes an entity from the repo
	 *
	 * @param   object $entity The entity to delete
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be deleted
	 */
	public function delete($entity);

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit();

	/**
	 * Define a condition.
	 *
	 * @param   mixed  $lValue The left value for the comparision
	 * @param   string $op     The comparision operator, one of the \Joomla\ORM\Finder\Operator constants
	 * @param   mixed  $rValue The right value for the comparision
	 *
	 * @return  EntityFinderInterface  $this for chaining
	 */
	public function restrictTo($lValue, $op, $rValue);
}
