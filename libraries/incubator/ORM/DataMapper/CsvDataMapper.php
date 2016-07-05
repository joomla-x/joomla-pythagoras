<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\DataMapper;

use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\InvalidOperatorException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;

/**
 * Class CsvDataMapper
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class CsvDataMapper implements DataMapperInterface, EntityFinderInterface, CollectionFinderInterface
{
	/** @var  array  Row cache */
	private $rows = null;

	/** @var  string  Name of the data file */
	private $dataFile;

	/** @var  string  Name of the definition file */
	private $definitionFile;

	/** @var string  Class of the entity */
	private $entityClass;

	/** @var  array  Conditions */
	private $conditions = [];

	/** @var  array  Ordering instructions */
	private $ordering = [];

	/**
	 * CsvDataMapper constructor.
	 *
	 * @param   string $entityClass    The class name of the entity
	 * @param   string $definitionFile The definition file name
	 * @param   string $dataFile       The data file name
	 */
	public function __construct($entityClass, $definitionFile, $dataFile)
	{
		$this->entityClass    = $entityClass;
		$this->definitionFile = $definitionFile;
		$this->dataFile       = $dataFile;
	}

	/**
	 * Find an entity using its id.
	 *
	 * getById() is a convenience method, It is equivalent to
	 * ->getOne()->with('id', \Joomla\ORM\Finder\Operator::EQUAL, '$id)->get()
	 *
	 * @param   mixed $id The id value
	 *
	 * @return  object  The requested entity
	 *
	 * @throws  EntityNotFoundException  if the entity does not exist
	 * @throws  OrmException  if there was an error getting the entity
	 */
	public function getById($id)
	{
		return $this->with('id', Operator::EQUAL, $id)->getItem();
	}

	/**
	 * Find a single entity.
	 *
	 * @return  EntityFinderInterface  The responsible Finder object
	 *
	 * @throws  OrmException  if there was an error getting the entity
	 */
	public function findOne()
	{
		return $this;
	}

	/**
	 * Find multiple entities.
	 *
	 * @return  CollectionFinderInterface  The responsible Finder object
	 *
	 * @throws  OrmException  if there was an error getting the entities
	 */
	public function findAll()
	{
		return $this;
	}

	/**
	 * Inserts an entity to the storage
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be inserted
	 */
	public function insert($entity)
	{
		if (is_null($this->rows))
		{
			$this->loadData();
		}

		if (empty($entity->id))
		{
			$entity->id = 0;

			foreach ($this->rows as $row)
			{
				$entity->id = max($entity->id, $row['id']);
			}

			$entity->id += 1;
		}
		else
		{
			foreach ($this->rows as $row)
			{
				if ($entity->id == $row['id'])
				{
					throw new OrmException("Entity with id {$entity->id} already exists.");
				}
			}
		}

		$this->rows[] = get_object_vars($entity);
	}

	/**
	 * Updates an entity in the storage
	 *
	 * @param   object $entity The entity to insert
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be updated
	 */
	public function update($entity)
	{
		if (is_null($this->rows))
		{
			$this->loadData();
		}

		foreach ($this->rows as $index => $row)
		{
			if ($entity->id == $row['id'])
			{
				$this->rows[$index] = get_object_vars($entity);

				return;
			}
		}

		throw new OrmException("Entity with id {$entity->id} not found.");
	}

	/**
	 * Deletes an entity from the storage
	 *
	 * @param   object $entity The entity to delete
	 *
	 * @return  void
	 *
	 * @throws  OrmException  if the entity could not be deleted
	 */
	public function delete($entity)
	{
		if (is_null($this->rows))
		{
			$this->loadData();
		}

		foreach ($this->rows as $index => $row)
		{
			if ($entity->id == $row['id'])
			{
				unset($this->rows[$index]);

				return;
			}
		}

		throw new OrmException("Entity with id {$entity->id} not found.");
	}

	/**
	 * Set the ordering.
	 *
	 * @param   string $column    The name of the ordering column
	 * @param   string $direction One of 'ASC' (ascending) or 'DESC' (descending)
	 *
	 * @return  $this
	 */
	public function orderBy($column, $direction = 'ASC')
	{
		$orderVal         = [
			'ASC'  => 1,
			'DESC' => -1
		];
		$this->ordering[] = [
			'column'    => $column,
			'direction' => $orderVal[strtoupper($direction)]
		];

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
		if (is_null($this->rows))
		{
			$this->loadData();
		}

		$matches = $this->rows;
		$matches = $this->applyConditions($matches);
		$matches = $this->applyOrdering($matches);

		if (!empty($this->columns))
		{
			$result = $this->applyColumns($matches);
		}
		else
		{
			$result = $this->castToEntity($matches);
		}

		return array_slice($result, $start, $count);
	}

	/**
	 * Define the columns to be retrieved.
	 *
	 * @param   array $columns The column names
	 *
	 * @return  $this
	 */
	public function columns($columns)
	{
		if (!is_array($columns))
		{
			$columns = preg_split('~\s*,\s*~', trim($columns));
		}

		$this->columns = $columns;

		return $this;
	}

	/**
	 * Define a condition.
	 *
	 * @param   mixed  $lValue The left value for the comparision
	 * @param   string $op     The comparision operator, one of the \Joomla\ORM\Finder\Operator constants
	 * @param   mixed  $rValue The right value for the comparision
	 *
	 * @return  $this
	 */
	public function with($lValue, $op, $rValue)
	{
		$this->conditions[] = [
			'field' => $lValue,
			'op'    => $op,
			'value' => $rValue
		];

		return $this;
	}

	/**
	 * Fetch the entity
	 *
	 * @return  object
	 *
	 * @throws  EntityNotFoundException  if the specified entity does not exist.
	 */
	public function getItem()
	{
		$items = $this->getItems();

		if (empty($items))
		{
			throw new EntityNotFoundException;
		}

		return $items[0];
	}

	/**
	 * Load the data from the file
	 *
	 * @return  void
	 */
	protected function loadData()
	{
		$fh   = fopen($this->dataFile, 'r');
		$keys = fgetcsv($fh);

		$this->rows = [];

		while (!feof($fh))
		{
			$row = fgetcsv($fh);

			if ($row === false)
			{
				break;
			}

			$this->rows[] = array_combine($keys, $row);
		}

		fclose($fh);
	}

	/**
	 * Apply the conditions
	 *
	 * @param   array $matches The records
	 *
	 * @return  array
	 */
	private function applyConditions($matches)
	{
		foreach ($this->conditions as $condition)
		{
			$matches = $this->filter($matches, $condition);
		}

		return $matches;
	}

	/**
	 * Apply the ordering
	 *
	 * @param   array $matches The records
	 *
	 * @return  array
	 */
	private function applyOrdering($matches)
	{
		foreach ($this->ordering as $ordering)
		{
			usort(
				$matches,
				function ($aRow, $bRow) use ($ordering) {
					$a = $aRow[$ordering['column']];
					$b = $bRow[$ordering['column']];

					return $ordering['direction'] * ($a == $b ? 0 : ($a < $b ? -1 : 1));
				}
			);
		}

		return $matches;
	}

	/**
	 * Apply the columns
	 *
	 * @param   array $matches The records
	 *
	 * @return  array
	 */
	private function applyColumns($matches)
	{
		if (empty($this->columns) || in_array('*', $this->columns))
		{
			return $matches;
		}

		$availableColumns = array_keys(reset($matches));
		$requestedColumns = $this->columns;

		foreach (array_diff($availableColumns, $requestedColumns) as $remove)
		{
			foreach ($matches as &$match)
			{
				unset($match[$remove]);
			}
		}

		return $matches;
	}

	/**
	 * Cast array to entity
	 *
	 * @param   array $matches The records
	 *
	 * @return  array
	 */
	private function castToEntity($matches)
	{
		$result = [];

		foreach ($matches as $match)
		{
			$entity = new $this->entityClass;

			foreach ($match as $key => $value)
			{
				$entity->{$key} = $value;
			}

			$result[] = $entity;
			unset($entity);
		}

		return $result;
	}

	/**
	 * Apply a condition to a record set
	 *
	 * @param   array $rows      The data rows
	 * @param   array $condition The filter condition
	 *
	 * @return array
	 */
	protected function filter($rows, $condition)
	{
		$result = [];

		foreach ($rows as $row)
		{
			$actual = $row[$condition['field']] ? $row[$condition['field']] : null;

			switch ($condition['op'])
			{
				case Operator::EQUAL:
					if ($actual == $condition['value'])
					{
						$result[] = $row;
					}
					break;

				case Operator::NOT_EQUAL:
					if ($actual != $condition['value'])
					{
						$result[] = $row;
					}
					break;

				case Operator::GREATER_THAN:
					if ($actual > $condition['value'])
					{
						$result[] = $row;
					}
					break;

				case Operator::GREATER_OR_EQUAL:
					if ($actual >= $condition['value'])
					{
						$result[] = $row;
					}
					break;

				case Operator::LESS_THAN:
					if ($actual < $condition['value'])
					{
						$result[] = $row;
					}
					break;

				case Operator::LESS_OR_EQUAL:
					if ($actual <= $condition['value'])
					{
						$result[] = $row;
					}
					break;

				case Operator::CONTAINS:
					if (strpos($actual, $condition['value']) !== false)
					{
						$result[] = $row;
					}
					break;

				case Operator::STARTS_WITH:
					if (preg_match('~^' . preg_quote($condition['value'], '~') . '~', $actual))
					{
						$result[] = $row;
					}
					break;

				case Operator::ENDS_WITH:
					if (preg_match('~' . preg_quote($condition['value'], '$~') . '~', $actual))
					{
						$result[] = $row;
					}
					break;

				case Operator::MATCHES:
					if (preg_match('~' . str_replace('~', '\~', $condition['value']) . '~', $actual))
					{
						$result[] = $row;
					}
					break;

				case Operator::IN:
					if (in_array($actual, $condition['value']))
					{
						$result[] = $row;
					}
					break;

				default:
					throw new InvalidOperatorException;
					break;
			}
		}

		return $result;
	}

	/**
	 * Persists all changes
	 *
	 * @return void
	 */
	public function commit()
	{
		$fh   = fopen($this->dataFile, 'w');
		$keys = array_keys($this->rows[0]);

		fputcsv($fh, $keys);

		foreach ($this->rows as $row)
		{
			fputcsv($fh, $row);
		}

		fclose($fh);
	}
}
