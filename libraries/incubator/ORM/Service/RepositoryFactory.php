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
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineTransactor;
use Joomla\ORM\UnitOfWork\ChangeTracker;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWork;
use Joomla\String\Inflector;

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

	/** @var  IdAccessorRegistry The Id Accessor Registry */
	private $idAccessorRegistry;

	/** @var EntityBuilder The entity builder */
	private $builder;

	/** @var  RepositoryInterface[] */
	private $repositories;

	/** @var UnitOfWork The unit of work to use in the tests */
	private $unitOfWork = null;

	/** @var EntityRegistry The entity registry to use in tests */
	private $entityRegistry = null;

	/** @var Inflector The inflector */
	private $inflector;

	/**
	 * RepositoryFactory constructor.
	 *
	 * @param array $config
	 * @param TransactionInterface $transactor
	 */
	public function __construct(array $config, $transactor)
	{
		$this->config             = $config;
		$this->inflector          = Inflector::getInstance();
		$this->idAccessorRegistry = new IdAccessorRegistry();

		$this->builder = $this->createEntityBuilder($this->config['definitionPath']);

		$changeTracker        = new ChangeTracker;
		$this->entityRegistry = new EntityRegistry($this->idAccessorRegistry, $changeTracker);
		$this->unitOfWork     = new UnitOfWork(
			$this->entityRegistry,
			$this->idAccessorRegistry,
			$changeTracker,
			$transactor
		);
	}

	/**
	 * @return IdAccessorRegistry
	 */
	public function getIdAccessorRegistry()
	{
		return $this->idAccessorRegistry;
	}

	/**
	 * @param $entityClass
	 *
	 * @return Repository
	 */
	public function forEntity($entityClass)
	{
		if (!isset($this->repositories[$entityClass]))
		{
			$dataMapperClass = $this->config[$entityClass]['dataMapper'];
			$dataPath        = $this->config['dataPath'];
			switch ($dataMapperClass)
			{
				case CsvDataMapper::class:
					static $gateway = null;

					if (is_null($gateway))
					{
						$gateway = new CsvDataGateway($dataPath);
					}

					$dataMapper = new CsvDataMapper(
						$gateway,
						$entityClass,
						$this->builder,
						basename($this->config[$entityClass]['data'], '.csv')
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
						$this->builder,
						$this->config[$entityClass]['table']
					);
					break;

				default:
					throw new OrmException("Unknown data mapper '$dataMapperClass'");
					break;
			}

			$this->unitOfWork->registerDataMapper($entityClass, $dataMapper);
			
			$this->repositories[$entityClass] = new Repository($entityClass, $dataMapper, $this->unitOfWork);
		}

		return $this->repositories[$entityClass];
	}

	/**
	 * @return EntityRegistry
	 */
	public function getEntityRegistry()
	{
		return $this->entityRegistry;
	}

	/**
	 * @return UnitOfWork
	 */
	public function getUnitOfWork()
	{
		return $this->unitOfWork;
	}

	/**
	 * @param $dataDirectory
	 *
	 * @return EntityBuilder
	 */
	private function createEntityBuilder($dataDirectory)
	{
		$strategy = new RecursiveDirectoryStrategy($dataDirectory);
		$locator  = new Locator([$strategy]);
		$builder  = new EntityBuilder($locator, $this->config, $this->idAccessorRegistry, $this);

		return $builder;
	}
}
