<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Persistor\PersistorInterface;
use Joomla\ORM\Validator\ValidatorInterface;

/**
 * Interface RepositoryInterface
 *
 * @package  Joomla/orm
 * @since    1.0
 */
interface RepositoryInterface
{
	/**
	 * Create a new entity.
	 *
	 * @return  EntityInterface  A new instance of the entity
	 */
	public function create();

	/**
	 * Find an entity using its id.
	 *
	 * findById() is a convenience method, It is equivalent to
	 * ->findOne()->with('id', \Joomla\ORM\Finder\Operator::EQUAL, '$id)->get()
	 *
	 * @param   mixed $id The id value
	 *
	 * @return  EntityInterface  The requested entity
	 *
	 * @throws  EntityNotFoundException  if the entity does not exist
	 */
	public function findById($id);

	/**
	 * Find a single entity.
	 *
	 * @return  EntityFinderInterface  The responsible Finder object
	 */
	public function findOne();

	/**
	 * Find multiple entities.
	 *
	 * @return  CollectionFinderInterface  The responsible Finder object
	 */
	public function findAll();

	/**
	 * Get the persistor
	 *
	 * @return  PersistorInterface
	 */
	public function persistor();
}
