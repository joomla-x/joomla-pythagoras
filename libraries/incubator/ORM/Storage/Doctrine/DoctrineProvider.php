<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\ORM\Storage\Doctrine;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Storage\StorageProviderInterface;

/**
 * Class DoctrineProvider
 *
 * @package Joomla/ORM
 *
 * @since 1.0
 */
class DoctrineProvider implements StorageProviderInterface
{

	/** @var  string The dsn url */
	private $dsn;

	/** @var  string The table name */
	private $tableName;

	/** @var EntityBuilder */
	private $builder = null;

	/**
	 * DoctrineProvider constructor.
	 *
	 * @param string $dsn
	 * @param EntityBuilder $builder
	 * @param string $tableName
	 */
	public function __construct($dsn, EntityBuilder $builder, $tableName)
	{
		$this->dsn = $dsn;
		$this->builder = $builder;
		$this->tableName = $tableName;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Joomla\ORM\Storage\StorageProviderInterface::getEntityFinder()
	 */
	public function getEntityFinder($entityName)
	{
		return new DoctrineEntityFinder($this->getConnection(), $this->tableName, $entityName, $this->builder);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Joomla\ORM\Storage\StorageProviderInterface::getCollectionFinder()
	 */
	public function getCollectionFinder($entityName)
	{
		return new DoctrineCollectionFinder($this->getConnection(), $this->tableName, $entityName, $this->builder);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Joomla\ORM\Storage\StorageProviderInterface::getPersistor()
	 */
	public function getPersistor($entityName)
	{
		return new DoctrinePersistor($this->getConnection(), $this->tableName);
	}

	/**
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	private function getConnection()
	{
		return DriverManager::getConnection([
				'url' => $this->dsn
		]);
	}
}
