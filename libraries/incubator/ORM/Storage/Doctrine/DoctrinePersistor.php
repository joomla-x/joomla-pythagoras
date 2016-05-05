<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Doctrine;

use Doctrine\DBAL\Connection;
use Joomla\ORM\Entity\EntityInterface;
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

	/** @var string $tableName */
	private $tableName = null;

	/**
	 * DoctrinePersistor constructor.
	 *
	 * @param   Connection    $connection The database connection
	 * @param   string        $tableName  The name of the table
	 */
	public function __construct(Connection $connection, $tableName)
	{
		$this->connection = $connection;
		$this->tableName  = $tableName;
	}

	/**
	 * Store an entity.
	 *
	 * @param   EntityInterface  $entity  The entity to store
	 *
	 * @return  void
	 */
	public function store(EntityInterface $entity)
	{
		$data = $entity->asArray();

		foreach ($data as $index => $value)
		{
			if (strpos($index, '@') === 0)
			{
				unset($data[$index]);
			}
		}

		$key = $entity->key();
		$id  = $entity->$key;

		if (!empty($id))
		{
			$this->connection->update(
				$this->tableName,
				$data,
				[
					$key => $id
				]
			);
		}
		else
		{
			unset($data[$key]);
			$this->connection->insert($this->tableName, $data);
		}
	}

	/**
	 * Delete an entity.
	 *
	 * @param   EntityInterface  $entity  The entity to sanitise
	 *
	 * @return  void
	 */
	public function delete(EntityInterface $entity)
	{
		$key = $entity->key();
		$id  = $entity->$key;

		if (empty($id))
		{
			return;
		}

		$this->connection->delete(
			$this->tableName,
			[
				$key => $entity->$key
			]
		);
	}
}
