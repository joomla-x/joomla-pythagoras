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
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Storage\PersistorInterface;
use Joomla\String\Normalise;

/**
 * Class DoctrineCollectionPersistor
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class DoctrinePersistor implements PersistorInterface
{
	/** @var Connection the connection to work on */
	private $connection = null;

	/** @var string */
	private $tableName = null;

	/** @var  EntityBuilder */
	private $builder;

	/** @var EntityRegistry */
	private $entityRegistry;

	/**
	 * DoctrinePersistor constructor.
	 *
	 * @param   Connection      $connection      The database connection
	 * @param   string          $tableName       The name of the table
	 * @param   EntityBuilder   $builder         The entity builder
	 * @param   EntityRegistry  $entityRegistry  The EntityRegistry
	 */
	public function __construct(Connection $connection, $tableName, EntityBuilder $builder, EntityRegistry $entityRegistry)
	{
		$this->connection     = $connection;
		$this->tableName      = $tableName;
		$this->builder        = $builder;
		$this->entityRegistry = $entityRegistry;
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
		$data     = $this->builder->reduce($entity);
		$entityId = $this->entityRegistry->getEntityId($entity);

		try
		{
			$this->connection->insert($this->tableName, $data);

			if (empty($entityId))
			{
				$this->entityRegistry->setEntityId($entity, $this->connection->lastInsertId());
			}

			$this->builder->resolve($entity);
		}
		catch (\Exception $e)
		{
			throw new OrmException("Entity with id {$entityId} already exists.\n" . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Update an entity.
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 */
	public function update($entity)
	{
		$identifier = $this->getIdentifier($entity);
		$data       = $this->builder->reduce($entity);

		$this->connection->update($this->tableName, $data, $identifier);

		$this->builder->resolve($entity);
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
		$identifier   = $this->getIdentifier($entity);
		$affectedRows = $this->connection->delete($this->tableName, $identifier);

		if ($affectedRows == 0)
		{
			throw new OrmException("Entity not found.");
		}
	}

	/**
	 * Gets the identifier for an entity
	 *
	 * @param   object $entity The entity
	 *
	 * @return  array  The identifier as key-value pair(s)
	 */
	protected function getIdentifier($entity)
	{
		$entityId = json_decode($this->entityRegistry->getEntityId($entity), true);

		if (is_array($entityId))
		{
			$identifier = [];

			foreach ($entityId as $key => $value)
			{
				$key              = strtolower(Normalise::toUnderscoreSeparated(Normalise::fromCamelCase($key)));
				$identifier[$key] = $value;
			}

			return $identifier;
		}

		$primary = $this->builder->getMeta(get_class($entity))->primary;

		return [$primary => $entityId];
	}
}
