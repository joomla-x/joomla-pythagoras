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

	/** @var  array The parameters */
	private $parameters;

	/** @var EntityBuilder */
	private $builder = null;

	/**
	 * DoctrineProvider constructor.
	 *
	 * @param array $parameters
	 *        	The parameters to create the connection from
	 * @param EntityBuilder $builder
	 *        	The parameters to create the connection from
	 */
	public function __construct(array $parameters, EntityBuilder $builder)
	{
		$this->parameters = $parameters;
		$this->builder = $builder;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Storage\StorageProviderInterface::getEntityFinder()
	 */
	public function getEntityFinder($entityName)
	{
		$parameters = $this->parameters;
		$parameters['entity_name'] = $entityName;
		return new DoctrineEntityFinder($this->getConnection(), $parameters, $this->builder);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Storage\StorageProviderInterface::getCollectionFinder()
	 */
	public function getCollectionFinder($entityName)
	{
		$parameters = $this->parameters;
		$parameters['entity_name'] = $entityName;
		return new DoctrineCollectionFinder($this->getConnection(), $parameters, $this->builder);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Storage\StorageProviderInterface::getPersistor()
	 */
	public function getPersistor($entityName)
	{
		$parameters = $this->parameters;
		$parameters['entity_name'] = $entityName;
		return new DoctrinePersistor($this->getConnection(), $parameters);
	}

	/**
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	private function getConnection()
	{
		$parameters = $this->parameters;
		if (key_exists('dsn', $parameters))
		{
			$parameters['url'] = str_replace('orm://', '', $parameters['dsn']);
			unset($parameters['dsn']);
		}
		$connection = DriverManager::getConnection($parameters);
		return $connection;
	}
}
