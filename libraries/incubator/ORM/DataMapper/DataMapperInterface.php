<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\DataMapper;


use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;

/**
 * Interface DataMapperInterface
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
interface DataMapperInterface
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
	 * Inserts an entity to the storage
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be inserted
	 */
	public function insert($entity);

	/**
	 * Updates an entity in the storage
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be updated
	 */
	public function update($entity);

	/**
	 * Deletes an entity from the storage
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
}
