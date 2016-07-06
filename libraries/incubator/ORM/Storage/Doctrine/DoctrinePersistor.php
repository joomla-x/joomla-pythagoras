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
use Joomla\ORM\Persistor\PersistorInterface;

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
	 *
	 * @return  void
	 */
	public function insert($entity)
	{
		$data = $this->builder->reduce($entity);

		try
		{
			$this->connection->insert($this->tableName, $data);

			if (empty($entity->id))
			{
				$entity->id = $this->connection->lastInsertId();
			}
		}
		catch (\Exception $e)
		{
			throw new OrmException("Entity with id {$entity->id} already exists.\n" . $e->getMessage());
		}
	}

	/**
	 * Update an entity.
	 *
	 * @param   object $entity The entity to store
	 *
	 * @return  void
	 */
	public function update($entity)
	{
		$data = $this->builder->reduce($entity);

		$affectedRows = $this->connection->update(
			$this->tableName,
			$data,
			[
				'id' => $entity->id
			]
		);

		if ($affectedRows == 0)
		{
			throw new OrmException("Entity with id {$entity->id} not found.");
		}
	}

	/**
	 * Delete an entity.
	 *
	 * @param   object $entity The entity to sanitise
	 *
	 * @return  void
	 */
	public function delete($entity)
	{
		$affectedRows = $this->connection->delete(
			$this->tableName,
			[
				'id' => $entity->id
			]
		);

		if ($affectedRows == 0)
		{
			throw new OrmException("Entity with id {$entity->id} not found.");
		}
	}
}
