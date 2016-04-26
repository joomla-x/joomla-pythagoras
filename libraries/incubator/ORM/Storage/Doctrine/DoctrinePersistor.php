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

	/**
	 *
	 * @var Connection the connection to work on
	 */
	private $connection = null;

	/**
	 *
	 * @var string[] the parameters
	 *
	 *      - table The table name to work on the
	 */
	private $parameters = null;

	public function __construct(Connection $connection, array $parameters)
	{
		$this->connection = $connection;
		$this->parameters = $parameters;
	}

	/**
	 *
	 * {@inheritDoc}
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
			$this->connection->update($this->parameters['table'], $data, [
					$entity->key() => $id
			]);
		}
		else
		{
			$this->connection->insert($this->parameters['table'], $entity->asArray());
		}
	}

	/**
	 *
	 * {@inheritDoc}
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
		$this->connection->delete($this->parameters['table'], [
				$entity->key() => $entity->{$entity->key()}
		]);
	}
}
