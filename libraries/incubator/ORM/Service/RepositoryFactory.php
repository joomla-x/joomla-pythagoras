<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Service;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Storage\Csv\CsvDataGateway;
use Joomla\ORM\Storage\Csv\CsvDataMapper;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWork;

/**
 * Repository Factory.
 *
 * @package Joomla/ORM
 *
 * @since   __DEPLOY_VERSION__
 */
class RepositoryFactory
{
	/** @var  array */
	private $config;

	/** @var EntityBuilder The entity builder */
	private $builder;

	/** @var  RepositoryInterface[] */
	private $repositories;

	/** @var UnitOfWork The unit of work to use in the tests */
	private $unitOfWork = null;

	/** @var EntityRegistry The entity registry to use in tests */
	private $entityRegistry = null;

	/**
	 * RepositoryFactory constructor.
	 *
	 * @api
	 *
	 * @param   array                 $config      The configuration
	 * @param   TransactionInterface  $transactor  A Transactor
	 */
	public function __construct(array $config, $transactor)
	{
		$this->config  = $config;
		$this->builder = $this->createEntityBuilder($this->config['definitionPath']);

		$this->entityRegistry = new EntityRegistry($this->builder);
		$this->unitOfWork     = new UnitOfWork(
			$this->entityRegistry,
			$transactor
		);
	}

	/**
	 * Gets a repository for an entity class.
	 *
	 * The Repository is created on first call. Any subsequent call will return the same instance, so the DataMapper
	 * can not be exchanged afterwards.
	 *
	 * On creation, the Repository gets supplied with the provided DataMapper. If data mapper is omitted, looks for
	 * a DataMapper for the given entity class registered to the UnitOfWork. If that fails, too, a new DataMapper is
	 * created using the information from the configuration.
	 *
	 * @api
	 *
	 * @param   string               $entityClass  The Eintity's class
	 * @param   DataMapperInterface  $dataMapper   An optional DataMapper
	 *
	 * @return  Repository
	 */
	public function forEntity($entityClass, DataMapperInterface $dataMapper = null)
	{
		if (!isset($this->repositories[$entityClass]))
		{
			if (empty($dataMapper))
			{
				try
				{
					$dataMapper = $this->unitOfWork->getDataMapper($entityClass);
				}
				catch (\RuntimeException $e)
				{
					$dataMapper      = $this->createDataMapper($entityClass);
				}
			}

			$this->unitOfWork->registerDataMapper($entityClass, $dataMapper);

			$this->repositories[$entityClass] = new Repository($entityClass, $dataMapper, $this->unitOfWork);
		}

		return $this->repositories[$entityClass];
	}

	/**
	 * Gets the IdAccessorRegistry
	 *
	 * @internal  This method is for internal ORM use only.
	 *
	 * @return  IdAccessorRegistry
	 */
	public function getIdAccessorRegistry()
	{
		return $this->entityRegistry->getIdAccessorRegistry();
	}

	/**
	 * Gets the EntityRegistry
	 *
	 * @internal  This method is for internal ORM use only.
	 *
	 * @return  EntityRegistry
	 */
	public function getEntityRegistry()
	{
		return $this->entityRegistry;
	}

	/**
	 * Gets the UnitOfWork
	 *
	 * @internal  This method is for internal ORM use only.
	 *
	 * @return  UnitOfWork
	 */
	public function getUnitOfWork()
	{
		return $this->unitOfWork;
	}

	/**
	 * Creates an EntityBuilder
	 *
	 * @param   string  $dataDirectory  The data directory
	 *
	 * @return  EntityBuilder
	 */
	private function createEntityBuilder($dataDirectory)
	{
		$strategy = new RecursiveDirectoryStrategy($dataDirectory);
		$locator  = new Locator([$strategy]);
		$builder  = new EntityBuilder($locator, $this->config, $this);

		return $builder;
	}

	/**
	 * Creates a DataMapper
	 *
	 * @param   string  $entityClass      The Entity's class
	 *
	 * @return  DataMapperInterface
	 */
	private function createDataMapper($entityClass)
	{
		switch ($this->config[$entityClass]['dataMapper'])
		{
			case CsvDataMapper::class:
				static $gateway = null;

				if (is_null($gateway))
				{
					$gateway = new CsvDataGateway($this->config['dataPath']);
				}

				$dataMapper = new CsvDataMapper(
					$gateway,
					$entityClass,
					basename($this->config[$entityClass]['data'], '.csv'),
					$this->entityRegistry
				);
				break;

			case DoctrineDataMapper::class:
				static $connection = null;

				if (is_null($connection))
				{
					$connection = DriverManager::getConnection(['url' => $this->config['databaseUrl']]);
				}

				$dataMapper = new DoctrineDataMapper(
					$connection,
					$entityClass,
					$this->config[$entityClass]['table'],
					$this->entityRegistry
				);
				break;

			default:
				throw new OrmException("No data mapper '$entityClass'");
				break;
		}

		return $dataMapper;
	}
}
