<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\Entity;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Persistor\PersistorInterface;

/**
 * Class CsvModel
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class CsvModel implements EntityFinderInterface, CollectionFinderInterface, PersistorInterface
{
	const ENTITY = 0;
	const COLLECTION = 1;

	/** @var  integer  Finder mode, see class constants */
	private $mode;

	/** @var  array  Conditions */
	private $conditions = [];

	/** @var  array  Row cache */
	private $rows = null;

	/** @var  array  Ordering instructions */
	private $ordering = [];

	/** @var  array  List of columns to retrieve */
	private $columns = [];

	/** @var  EntityBuilder  The entity factory */
	private $builder;

	/** @var  string  Name of the data file */
	private $dataFile;

	/**
	 * CsvModel constructor.
	 *
	 * @param   array   $parameters The parameters
	 * @param   integer $mode     The finder mode, see class constants
	 */
	public function __construct($parameters, $mode)
	{
		$this->dataFile = str_replace('csv://', '', $parameters['dsn']);
		$this->mode     = $mode;

		$locator       = new Locator(
			[
				new RecursiveDirectoryStrategy(getcwd() . '/components'),
			]
		);
		$this->builder = new EntityBuilder($locator);
	}

	/**
	 * Set the ordering.
	 *
	 * @param   string $column    The name of the ordering column
	 * @param   string $direction One of 'ASC' (ascending) or 'DESC' (descending)
	 *
	 * @return  CollectionFinderInterface  $this for chaining
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
	 * Define the columns to be retrieved.
	 *
	 * @param   array $columns The column names
	 *
	 * @return  CollectionFinderInterface  $this for chaining
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
	 * @return  CollectionFinderInterface  $this for chaining
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
	 * @param   integer $count The number of matching entities to retrieve (collection mode only)
	 * @param   integer $start The index of the first entity to retrieve (collection mode only)
	 *
	 * @return  Entity|array
	 */
	public function get($count = null, $start = 0)
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

		if ($this->mode == self::ENTITY)
		{
			if (empty($result))
			{
				throw new EntityNotFoundException;
			}

			return array_shift($result);
		}
		else
		{
			return array_slice($result, $start, $count);
		}
	}

	/**
	 * Store an entity.
	 *
	 * @param   EntityInterface $entity The entity to store
	 *
	 * @return  void
	 */
	public function store(EntityInterface $entity)
	{
		echo "Storing {$entity->type()}#{$entity->id}\n";
		$this->rows = null;
	}

	/**
	 * Delete an entity.
	 *
	 * @param   EntityInterface $entity The entity to sanitise
	 *
	 * @return  void
	 */
	public function delete(EntityInterface $entity)
	{
		echo "Deleting {$entity->type()}#{$entity->id}\n";
		$this->rows = null;
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
					if (in_array($actual, preg_split('~\s*,\s*~', trim($condition['value']))))
					{
						$result[] = $row;
					}
					break;
			}
		}

		return $result;
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
			$entity = $this->builder->create('Article');
			$entity->bind($match);
			$result[] = $entity;
			unset($entity);
		}

		return $result;
	}
}
