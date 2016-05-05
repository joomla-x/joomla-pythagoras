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
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Finder\CollectionFinderInterface;

/**
 * Class DoctrineCollectionFinder
 *
 * @package Joomla/ORM
 *
 * @since 1.0
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

	public function __construct(Connection $connection, $tableName, $entityName, EntityBuilder $builder)
	{
		$this->connection = $connection;
		$this->tableName = $tableName;
		$this->entityName = $entityName;
		$this->builder = $builder;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Finder\CollectionFinderInterface::columns()
	 */
	public function columns($columns)
	{
		$this->columns = $columns;
		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Finder\CollectionFinderInterface::with()
	 */
	public function with($lValue, $op, $rValue)
	{
		if (!Operator::isValid($op))
		{
			return $this;
		}
		$this->conditions[$lValue . ' ' . $op . ' ?'] = $rValue;
		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Finder\CollectionFinderInterface::orderBy()
	 */
	public function orderBy($column, $direction = 'ASC')
	{
		$this->ordering[$column] = $direction;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Joomla\ORM\Finder\CollectionFinderInterface::getItems()
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
			$counter ++;
		}
		foreach ($this->ordering as $column => $order)
		{
			$builder->orderBy($column, $order);
		}

		$builder->setMaxResults($count);
		$builder->setFirstResult($start);

		$rows = $builder->execute()->fetchAll(\PDO::FETCH_ASSOC);
		if (!$rows)
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
