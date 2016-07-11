<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\UnitOfWork;

use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Storage\DataMapperInterface;

interface UnitOfWorkInterface
{
	/**
	 * Commits any entities that have been scheduled for insertion/updating/deletion
	 *
	 * @throws OrmException Thrown if there was an error committing the transaction
	 */
	public function commit();

	/**
	 * Detaches an entity from being managed
	 *
	 * @param object $entity The entity to detach
	 */
	public function detach($entity);

	/**
	 * Disposes of all data in this unit of work
	 */
	public function dispose();

	/**
	 * Gets the unit of work's entity registry
	 *
	 * @return EntityRegistry The entity registry used by the unit of work
	 */
	public function getEntityRegistry();

	/**
	 * Registers a data mapper for a class
	 * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
	 *
	 * @param string      $className  The name of the class whose data mapper we're registering
	 * @param DataMapperInterface $dataMapper The data mapper for the class
	 */
	public function registerDataMapper($className, DataMapperInterface $dataMapper);

	/**
	 * Schedules an entity for deletion
	 *
	 * @param object $entity The entity to schedule for deletion
	 */
	public function scheduleForDeletion($entity);

	/**
	 * Schedules an entity for insertion
	 *
	 * @param object $entity The entity to schedule for insertion
	 */
	public function scheduleForInsertion($entity);

	/**
	 * Schedules an entity for insertion
	 *
	 * @param object $entity The entity to schedule for insertion
	 */
	public function scheduleForUpdate($entity);

	/**
	 * Sets the database transactor
	 *
	 * @param TransactionInterface $transactor The transactor to use
	 */
	public function setTransactor(TransactionInterface $transactor);
}
