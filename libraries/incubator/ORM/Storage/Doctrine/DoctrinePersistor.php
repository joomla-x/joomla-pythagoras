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
 * @since 1.0
 */
class DoctrinePersistor implements PersistorInterface
{

	/** @var Connection the connection to work on */
	private $connection = null;

	/** @var string $tableName */
	private $tableName = null;

	public function __construct(Connection $connection, $tableName)
	{
		$this->connection = $connection;
		$this->tableName = $tableName;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Joomla\ORM\Persistor\PersistorInterface::store()
	 */
	public function store(EntityInterface $entity)
	{
		$data = $entity->asArray();
		foreach ($data as $key => $value)
		{
			if (strpos($key, '@') === 0)
			{
				unset($data[$key]);
			}
		}

		$id = $entity->{$entity->key()};
		if ($id)
		{
			$this->connection->update($this->tableName, $data, [
					$entity->key() => $id
			]);
		}
		else
		{
			unset($data[$entity->key()]);
			$this->connection->insert($this->tableName, $data);
		}
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Joomla\ORM\Persistor\PersistorInterface::delete()
	 */
	public function delete(EntityInterface $entity)
	{
		$id = $entity->{$entity->key()};
		if (!$id)
		{
			return;
		}
		$this->connection->delete($this->tableName, [
				$entity->key() => $entity->{$entity->key()}
		]);
	}
}
