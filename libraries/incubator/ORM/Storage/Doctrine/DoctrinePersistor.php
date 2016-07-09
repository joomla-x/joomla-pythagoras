<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Doctrine;

use Doctrine\DBAL\Connection;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Storage\PersistorInterface;

/**
 * Class DoctrineCollectionPersistor
 *
 * @package Joomla/ORM
 *
 * @since   1.0
 */
class DoctrinePersistor implements PersistorInterface
{
	/** @var Connection the connection to work on */
	private $connection = null;

	/** @var string */
	private $tableName = null;

	/** @var  EntityBuilder */
	private $builder;

	/**
	 * DoctrinePersistor constructor.
	 *
	 * @param   Connection    $connection The database connection
	 * @param   string        $tableName  The name of the table
	 * @param   EntityBuilder $builder    The entity builder
	 */
	public function __construct(Connection $connection, $tableName, $builder)
	{
		$this->connection = $connection;
		$this->tableName  = $tableName;
		$this->builder    = $builder;
	}

	/**
	 * Insert an entity.
	 *
	 * @param   object $entity The entity to store
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 */
	public function insert($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$data = $this->builder->reduce($entity);
		$entityId = $idAccessorRegistry->getEntityId($entity);

		try
		{
			$this->connection->insert($this->tableName, $data);

			if (empty($entityId))
			{
				$idAccessorRegistry->setEntityId($entity, $this->connection->lastInsertId());
			}
		}
		catch (\Exception $e)
		{
			throw new OrmException("Entity with id {$entityId} already exists.\n" . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Update an entity.
	 *
	 * @param   object             $entity The entity to insert
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 * 
	 * @return  void
	 */
	public function update($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$entityId = $idAccessorRegistry->getEntityId($entity);

		$data = $this->builder->reduce($entity);

		$affectedRows = $this->connection->update(
			$this->tableName,
			$data,
			[
				'id' => $entityId
			]
		);

		#if ($affectedRows == 0)
		#{
		#	throw new OrmException("Entity with id {$entityId} not found.");
		#}
	}

	/**
	 * Delete an entity.
	 *
	 * @param   object $entity The entity to sanitise
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 */
	public function delete($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$entityId = $idAccessorRegistry->getEntityId($entity);

		$affectedRows = $this->connection->delete(
			$this->tableName,
			[
				'id' => $entityId
			]
		);

		if ($affectedRows == 0)
		{
			throw new OrmException("Entity with id {$entityId} not found.");
		}
	}
}
