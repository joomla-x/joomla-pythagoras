<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Service;

use Doctrine\DBAL\Connection;
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

	/** @var  CsvDataGateway|Connection */
	private $connection;

	/** @var  array */
	private $connections;

	/** @var EntityBuilder The entity builder */
	private $builder;

	/** @var UnitOfWork The unit of work to use in the tests */
	private $unitOfWork = null;

	/** @var EntityRegistry The entity registry to use in tests */
	private $entityRegistry = null;

	/**
	 * RepositoryFactory constructor.
	 *
	 * @api
	 *
	 * @param   array                      $config     The configuration
	 * @param   CsvDataGateway|Connection  $connection The connection / gateway
	 * @param   TransactionInterface       $transactor A Transactor
	 */
	public function __construct(array $config, $connection, $transactor)
	{
		$this->config     = $config;
		$this->connection = $connection;
		$this->builder    = $this->createEntityBuilder(JPATH_ROOT . '/' . $this->config['definitionPath']);

		$this->entityRegistry = new EntityRegistry($this->builder);
		$this->unitOfWork     = new UnitOfWork(
			$this->entityRegistry,
			$transactor
		);

		$this->connections[get_class($connection)] = $connection;
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
	 * @param   string               $entityClass  The Entity's class
	 * @param   DataMapperInterface  $dataMapper   An optional DataMapper
	 *
	 * @return  RepositoryInterface
	 */
	public function forEntity($entityClass, DataMapperInterface $dataMapper = null)
	{
		if (empty($dataMapper))
		{
			try
			{
				$dataMapper = $this->unitOfWork->getDataMapper($entityClass);
			}
			catch (\RuntimeException $e)
			{
				$dataMapper = $this->createDataMapper($entityClass);
			}
		}

		$this->unitOfWork->registerDataMapper($entityClass, $dataMapper);

		return new Repository($entityClass, $dataMapper, $this->unitOfWork);
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
	 * Gets the EntityBuilder
	 *
	 * @internal  This method is for internal ORM use only.
	 *
	 * @return  EntityBuilder
	 */
	public function getEntityBuilder()
	{
		return $this->builder;
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
		$builder  = new EntityBuilder($locator, $this);

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
		$dataMapperClass = isset($this->config['dataMapper']) ? $this->config['dataMapper'] : DoctrineDataMapper::class;

		$meta = $this->builder->getMeta($entityClass);

		if ($meta->storage['type'] == 'api')
		{
			$dataMapperClass = $meta->storage['handler'];
		}

		switch ($dataMapperClass)
		{
			case CsvDataMapper::class:
				if (!isset($this->connections[CsvDataGateway::class]))
				{
					$this->connections[CsvDataGateway::class] = new CsvDataGateway($this->config['dataPath']);
				}

				$dataMapper = new CsvDataMapper(
					$this->connections[CsvDataGateway::class],
					$entityClass,
					$meta->storage['table'],
					$this->entityRegistry
				);
				break;

			case DoctrineDataMapper::class:
				if (!isset($this->connections[Connection::class]))
				{
					$this->connections[Connection::class] = DriverManager::getConnection(['url' => $this->config['databaseUrl']]);
				}

				$dataMapper = new DoctrineDataMapper(
					$this->connections[Connection::class],
					$entityClass,
					$meta->storage['table'],
					$this->entityRegistry
				);
				break;

			default:
				throw new OrmException("No data mapper '$dataMapperClass' for '$entityClass'");
				break;
		}

		return $dataMapper;
	}

	public function getSchemaManager()
	{
		if (method_exists($this->connection, 'getSchemaManager'))
		{
			return $this->connection->getSchemaManager();
		}

		return null;
	}

	public function getConnection($type = null)
	{
		if (!isset($this->connections[CsvDataGateway::class]) && isset($this->config['dataPath']))
		{
			$this->connections[CsvDataGateway::class] = new CsvDataGateway(JPATH_ROOT . '/' . $this->config['dataPath']);
		}

		if (!isset($this->connections[Connection::class]) && isset($this->config['databaseUrl']))
		{
			$databaseUrl = $this->config['databaseUrl'];
			$url = parse_url($databaseUrl);

			if ($url['schema'] == 'sqlite')
			{
				$databaseUrl = str_replace('sqlite://', 'sqlite://' . JPATH_ROOT, $databaseUrl);
			}

			$this->connections[Connection::class] = DriverManager::getConnection(['url' => $databaseUrl]);
		}

		return $this->connections[$type];
	}
}
