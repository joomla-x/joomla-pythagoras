<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Mocks;

use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWork;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;

/**
 * Mocks the unit of work for testing
 */
class UnitOfWorkAccessDecorator implements UnitOfWorkInterface
{
	/** @var  UnitOfWorkInterface */
	private $unitOfWork;

	/** @var  \ReflectionClass */
	private $reflection;

	public function __construct(UnitOfWork $unitOfWork)
	{
		$this->unitOfWork = $unitOfWork;
		$this->reflection = new \ReflectionClass(UnitOfWork::class);
	}

	/**
	 * Checks for any changes made to entities, and if any are found, they're scheduled for update
	 */
	public function checkForUpdates()
	{
		$method = $this->reflection->getMethod('checkForUpdates');
		$method->setAccessible(true);
		$method->invoke($this->unitOfWork);
	}

	/**
	 * Gets the list of entities that are scheduled for deletion
	 *
	 * @return object[] The list of entities scheduled for deletion
	 */
	public function getScheduledEntityDeletions()
	{
		$method = $this->reflection->getMethod('getScheduledEntityDeletions');
		$method->setAccessible(true);

		return $method->invoke($this->unitOfWork);
	}

	/**
	 * Gets the list of entities that are scheduled for insertion
	 *
	 * @return object[] The list of entities scheduled for insertion
	 */
	public function getScheduledEntityInsertions()
	{
		$method = $this->reflection->getMethod('getScheduledEntityInsertions');
		$method->setAccessible(true);

		return $method->invoke($this->unitOfWork);
	}

	/**
	 * Gets the list of entities that are scheduled for update
	 *
	 * @return object[] The list of entities scheduled for update
	 */
	public function getScheduledEntityUpdates()
	{
		$method = $this->reflection->getMethod('getScheduledEntityUpdates');
		$method->setAccessible(true);

		return $method->invoke($this->unitOfWork);
	}

	/**
	 * Gets the data mapper for the input class
	 *
	 * @param string $className The name of the class whose data mapper we're searching for
	 *
	 * @return DataMapperInterface The data mapper for the input class
	 * @throws \RuntimeException Thrown if there was no data mapper for the input class name
	 */
	public function getDataMapper($className)
	{
		$method = $this->reflection->getMethod('getDataMapper');
		$method->setAccessible(true);

		return $method->invokeArgs($this->unitOfWork, [$className]);
	}

	/**
	 * Commits any entities that have been scheduled for insertion/updating/deletion
	 *
	 * @throws OrmException Thrown if there was an error committing the transaction
	 */
	public function commit()
	{
		$this->unitOfWork->commit();
	}

	/**
	 * Detaches an entity from being managed
	 *
	 * @param object $entity The entity to detach
	 */
	public function detach($entity)
	{
		$this->unitOfWork->detach($entity);
	}

	/**
	 * Disposes of all data in this unit of work
	 */
	public function dispose()
	{
		$this->unitOfWork->dispose();
	}

	/**
	 * Gets the unit of work's entity registry
	 *
	 * @return EntityRegistry The entity registry used by the unit of work
	 */
	public function getEntityRegistry()
	{
		return $this->unitOfWork->getEntityRegistry();
	}

	/**
	 * Registers a data mapper for a class
	 * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
	 *
	 * @param string              $className  The name of the class whose data mapper we're registering
	 * @param DataMapperInterface $dataMapper The data mapper for the class
	 */
	public function registerDataMapper($className, DataMapperInterface $dataMapper)
	{
		$this->unitOfWork->registerDataMapper($className, $dataMapper);
	}

	/**
	 * Schedules an entity for deletion
	 *
	 * @param object $entity The entity to schedule for deletion
	 */
	public function scheduleForDeletion($entity)
	{
		$this->unitOfWork->scheduleForDeletion($entity);
	}

	/**
	 * Schedules an entity for insertion
	 *
	 * @param object $entity The entity to schedule for insertion
	 */
	public function scheduleForInsertion($entity)
	{
		$this->unitOfWork->scheduleForInsertion($entity);
	}

	/**
	 * Schedules an entity for insertion
	 *
	 * @param object $entity The entity to schedule for insertion
	 */
	public function scheduleForUpdate($entity)
	{
		$this->unitOfWork->scheduleForUpdate($entity);
	}

	/**
	 * Sets the database transactor
	 *
	 * @param TransactionInterface $transactor The transactor to use
	 */
	public function setTransactor(TransactionInterface $transactor)
	{
		$this->unitOfWork->setTransactor($transactor);
	}
}
