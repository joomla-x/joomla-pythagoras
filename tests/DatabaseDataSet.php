<?php

namespace Joomla\Tests;

use PHPUnit_Extensions_Database_DataSet_AbstractDataSet;
use PHPUnit_Extensions_Database_DataSet_DefaultTable;
use PHPUnit_Extensions_Database_DataSet_DefaultTableIterator;
use PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData;
use PHPUnit_Extensions_Database_DataSet_ITable;
use PHPUnit_Extensions_Database_DataSet_ITableIterator;

/**
 * Class DatabaseDataSet
 *
 * @package Joomla\Tests
 */
class DatabaseDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
	/**
	 * @var array
	 */
	protected $tables = [];

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		foreach ($data as $tableName => $rows)
		{
			$columns = [];

			if (isset($rows[0]))
			{
				$columns = array_keys($rows[0]);
			}

			$metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
			$table    = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

			foreach ($rows as $row)
			{
				$table->addRow($row);
			}

			$this->tables[$tableName] = $table;
		}
	}

	/**
	 * Creates an iterator over the tables in the data set. If $reverse is
	 * true a reverse iterator will be returned.
	 *
	 * @param  bool $reverse
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
	 */
	protected function createIterator($reverse = false)
	{
		return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
	}

	/**
	 * Returns a table object for the given table.
	 *
	 * @param  string $tableName
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_ITable
	 */
	public function getTable($tableName)
	{
		if (!isset($this->tables[$tableName]))
		{
			throw new \InvalidArgumentException("$tableName is not a table in the current database.");
		}

		return $this->tables[$tableName];
	}
}
