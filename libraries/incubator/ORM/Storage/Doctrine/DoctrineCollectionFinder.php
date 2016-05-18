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
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\Operator;

/**
 * Class DoctrineCollectionFinder
 *
 * @package Joomla/ORM
 *
 * @since   1.0
 */
class DoctrineCollectionFinder implements CollectionFinderInterface
{
	/** @var string[] the columns */
	private $columns = [];

	/** @var string[] the conditions */
	private $conditions = [];

	/** @var string[] the ordering */
	private $ordering = [];

	/** @var Connection the connection to work on */
	private $connection = null;

	/** @var string $tableName */
	private $tableName = null;

	/** @var string $entityName */
	private $entityName = null;

	/** @var EntityBuilder */
	private $builder = null;

	/**
	 * DoctrineCollectionFinder constructor.
	 *
	 * @param   Connection    $connection The database connection
	 * @param   string        $tableName  The name of the table
	 * @param   string        $entityName The name of the entity
	 * @param   EntityBuilder $builder    The entity builder
	 */
	public function __construct(Connection $connection, $tableName, $entityName, EntityBuilder $builder)
	{
		$this->connection = $connection;
		$this->tableName  = $tableName;
		$this->entityName = $entityName;
		$this->builder    = $builder;
	}

	/**
	 * Define the columns to be retrieved.
	 *
	 * @param   array  $columns  The column names
	 *
	 * @return  CollectionFinderInterface  $this for chaining
	 */
	public function columns($columns)
	{
		$this->columns = $columns;

		return $this;
	}

	/**
	 * Define a condition.
	 *
	 * @param   mixed   $lValue  The left value for the comparision
	 * @param   string  $op      The comparision operator, one of the \Joomla\ORM\Finder\Operator constants
	 * @param   mixed   $rValue  The right value for the comparision
	 *
	 * @return  CollectionFinderInterface  $this for chaining
	 */
	public function with($lValue, $op, $rValue)
	{if(!$lValue){echo new \Exception();die;}
		if (!Operator::isValid($op))
		{
			return $this;
		}

		$this->conditions[$lValue . ' ' . $op . ' ?'] = $rValue;

		return $this;
	}

	/**
	 * Set the ordering.
	 *
	 * @param   string  $column     The name of the ordering column
	 * @param   string  $direction  One of 'ASC' (ascending) or 'DESC' (descending)
	 *
	 * @return  CollectionFinderInterface  $this for chaining
	 */
	public function orderBy($column, $direction = 'ASC')
	{
		$this->ordering[$column] = $direction;

		return $this;
	}

	/**
	 * Fetch the entities
	 *
	 * @param   int $count The number of matching entities to retrieve
	 * @param   int $start The index of the first entity to retrieve
	 *
	 * @return  array
	 */
	public function getItems($count = null, $start = 0)
	{
		$builder = $this->connection->createQueryBuilder();
		$builder->select(!$this->columns ? '*' : $this->columns);
		$builder->from($this->tableName);

		$counter = 0;

		foreach ($this->conditions as $left => $value)
		{
			$builder->andWhere($left);
			$builder->setParameter($counter, $value);
			$counter++;
		}

		foreach ($this->ordering as $column => $order)
		{
			$builder->orderBy($column, $order);
		}

		$builder->setMaxResults($count);
		$builder->setFirstResult($start);

		$rows = $builder->execute()->fetchAll(\PDO::FETCH_ASSOC);

		if (empty($rows))
		{
			return [];
		}

		$data = [];

		foreach ($rows as $row)
		{
			$entity = $this->builder->create($this->entityName);
			$entity->bind($row);
			$data[] = $entity;
		}

		return $data;
	}
}
