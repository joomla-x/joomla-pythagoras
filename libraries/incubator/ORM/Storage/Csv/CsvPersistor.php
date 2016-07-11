<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Csv;

use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Storage\PersistorInterface;

/**
 * Class CsvCollectionPersistor
 *
 * @package Joomla/ORM
 *
 * @since   1.0
 */
class CsvPersistor implements PersistorInterface
{
	/** @var CsvDataGateway  */
	private $gateway;

	/** @var string */
	private $tableName = null;

	/** @var  EntityBuilder */
	private $builder;

	public function __construct(CsvDataGateway $gateway, $tableName, $builder)
	{
		$this->gateway = $gateway;
		$this->tableName = $tableName;
		$this->builder = $builder;
	}

	/**
	 * Insert an entity.
	 *
	 * @param   object             $entity The entity to store
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @throws OrmException
	 */
	public function insert($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$entityId = $idAccessorRegistry->getEntityId($entity);

		if (empty($entityId))
		{
			$id = 0;

			foreach ($this->gateway->getAll($this->tableName) as $row)
			{
				$id = max($id, $row['id']);
			}

			$idAccessorRegistry->setEntityId($entity, $id + 1);
		}

		$this->gateway->insert($this->tableName, $this->builder->reduce($entity));
		$this->builder->resolve($entity);
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
		$this->gateway->update($this->tableName, $this->builder->reduce($entity));
		$this->builder->resolve($entity);
	}

	/**
	 * Delete an entity.
	 *
	 * @param   object             $entity The entity to sanitise
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 */
	public function delete($entity, IdAccessorRegistry $idAccessorRegistry)
	{
		$this->gateway->delete($this->tableName, $this->builder->reduce($entity));
	}
}
