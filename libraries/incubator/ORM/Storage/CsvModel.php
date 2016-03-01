<?php

namespace Joomla\ORM\Storage;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Persistor\PersistorInterface;

class CsvModel implements EntityFinderInterface, CollectionFinderInterface, PersistorInterface
{
	const ENTITY = 0;
	const COLLECTION = 1;

	private $mode;
	private $conditions = [];
	private $rows = null;
	private $ordering = [];
	private $columns = [];

	private $builder;

	private $dataFile;

	public function __construct($dataFile, $mode)
	{
		$this->dataFile = $dataFile;
		$this->mode     = $mode;

		$locator = new Locator([
			new RecursiveDirectoryStrategy(getcwd() . '/components'),
		]);
		$this->builder = new EntityBuilder($locator);
	}

	public function orderBy($column, $direction = 'ASC')
	{
		$orderVal         = [
			'ASC'  => +1,
			'DESC' => -1
		];
		$this->ordering[] = [
			'column'    => $column,
			'direction' => $orderVal[strtoupper($direction)]
		];

		return $this;
	}

	public function columns($columns)
	{
		$this->columns = preg_split('~\s*,\s*~', trim($columns));

		return $this;
	}

	public function with($lValue, $op, $rValue)
	{
		$this->conditions[] = [
			'field' => $lValue,
			'op'    => $op,
			'value' => $rValue
		];

		return $this;
	}

	public function get($count = null, $start = 0)
	{
		if (is_null($this->rows))
		{
			$this->loadData();
		}

		if (empty($this->rows))
		{
			return [];
		}

		$matches = $this->rows;
		$matches = $this->applyConditions($matches);
		$matches = $this->applyOrdering($matches);
		$matches = $this->applyColumns($matches);
		$entities = $this->castToEntity($matches);

		return $this->mode == self::ENTITY ? array_shift($entities) : array_slice($entities, $start, $count);
	}

	public function store(EntityInterface $entity)
	{
		echo "Storing {$entity->type()}#{$entity->id}\n";
		$this->rows = null;
	}

	public function delete(EntityInterface $entity)
	{
		echo "Deleting {$entity->type()}#{$entity->id}\n";
		$this->rows = null;
	}

	/**
	 * @param $rows
	 * @param $condition
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

	protected function loadData()
	{
		$fh       = fopen($this->dataFile, 'r');
		$keys     = fgetcsv($fh);

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
	 * @param $matches
	 *
	 * @return array
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
	 * @param $matches
	 *
	 * @return mixed
	 */
	private function applyOrdering($matches)
	{
		foreach ($this->ordering as $ordering)
		{
			usort($matches, function ($aRow, $bRow) use ($ordering)
			{
				$a = $aRow[$ordering['column']];
				$b = $bRow[$ordering['column']];

				return $ordering['direction'] * ($a == $b ? 0 : ($a < $b ? -1 : +1));
			});
		}

		return $matches;
	}

	/**
	 * @param $matches
	 *
	 * @return mixed
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
