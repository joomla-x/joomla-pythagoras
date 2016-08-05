<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Csv;

use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Storage\PersistorInterface;
use Joomla\Tests\Unit\DumpTrait;

/**
 * Class CsvCollectionPersistor
 *
 * @package Joomla/ORM
 *
 * @since   __DEPLOY_VERSION__
 */
class CsvPersistor implements PersistorInterface
{
	/** @var CsvDataGateway */
	private $gateway;

	/** @var string */
	private $tableName = null;

	/** @var  EntityBuilder */
	private $builder;

	/** @var  EntityRegistry */
	private $entityRegistry;

	/**
	 * CsvPersistor constructor.
	 *
	 * @param   CsvDataGateway  $gateway         The data gateway
	 * @param   string          $tableName       The table name
	 * @param   EntityBuilder   $builder         The EntityBuilder
	 * @param   EntityRegistry  $entityRegistry  The EntityRegistry
	 */
	public function __construct(CsvDataGateway $gateway, $tableName, EntityBuilder $builder, EntityRegistry $entityRegistry)
	{
		$this->gateway        = $gateway;
		$this->tableName      = $tableName;
		$this->builder        = $builder;
		$this->entityRegistry = $entityRegistry;
	}

	/**
	 * Insert an entity.
	 *
	 * @param   object  $entity  The entity to store
	 *
	 * @return  void
	 * @throws  OrmException
	 */
	public function insert($entity)
	{
		$entityId = $this->entityRegistry->getEntityId($entity);

		if (empty($entityId))
		{
			$id = 0;

			foreach ($this->gateway->getAll($this->tableName) as $row)
			{
				$id = max($id, $row['id']);
			}

			$this->entityRegistry->setEntityId($entity, $id + 1);
		}

		$this->gateway->insert($this->tableName, $this->builder->reduce($entity));
		$this->builder->resolve($entity);
	}

	/**
	 * Update an entity.
	 *
	 * @param   object  $entity  The entity to insert
	 *
	 * @return  void
	 */
	public function update($entity)
	{
		$this->gateway->update($this->tableName, $this->builder->reduce($entity), $this->getIdentifier($entity));
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
		$this->gateway->delete($this->tableName, $this->builder->reduce($entity), $this->getIdentifier($entity));
	}

	/**
	 * Gets the identifier for an entity
	 *
	 * @param   object  $entity  The entity
	 *
	 * @return  array  The identifier as key-value pair(s)
	 */
	protected function getIdentifier($entity)
	{
		$primary    = $this->builder->getMeta(get_class($entity))->primary;
		$entityId   = $this->entityRegistry->getEntityId($entity);

		return [$primary => $entityId];
	}
}
