<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Csv;

/**
 * Class CsvDataGateway
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class CsvDataGateway
{
	/** @var  array  Column names */
	private $keys = [];

	/** @var  array  Row cache */
	private $rows = [];

	/** @var  array */
	private $snapshot;

	/** @var  string  Path of the data directory */
	private $dataPath;

	/**
	 * CsvDataGateway constructor.
	 *
	 * @param   string  $dataPath  The path to the CSV data
	 */
	public function __construct($dataPath)
	{
		$this->dataPath = $dataPath;
	}

	/**
	 * Find multiple entities.
	 *
	 * @param   string  $table  The name of the table
	 *
	 * @return  mixed
	 */
	public function getAll($table)
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		return $this->rows[$table];
	}

	/**
	 * Load the data from the file
	 *
	 * @param   string  $table  The name of the table
	 *
	 * @return  void
	 */
	protected function loadTable($table)
	{
		$fh                 = fopen($this->dataFile($table), 'r');
		$this->keys[$table] = fgetcsv($fh);

		$this->rows[$table] = [];

		while (!feof($fh))
		{
			$row = fgetcsv($fh);

			if ($row === false)
			{
				break;
			}

			$this->rows[$table][] = array_combine($this->keys[$table], $row);
		}

		fclose($fh);
	}

	/**
	 * Gets the path for the data file for a table
	 *
	 * @param   string  $table  The name of the table
	 *
	 * @return  string  The path to the data file
	 */
	protected function dataFile($table)
	{
		return $this->dataPath . '/' . $table . '.csv';
	}

	/**
	 * Inserts an entity to the storage
	 *
	 * @param   string  $table  The name of the table
	 * @param   array   $row    The data row
	 *
	 * @return  void
	 */
	public function insert($table, $row)
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		$data = $this->sanitiseRow($table, $row);

		$this->rows[$table][] = $data;
	}

	/**
	 * Sanitises a row
	 *
	 * @param   string  $table  The name of the table
	 * @param   array   $row    The data row
	 *
	 * @return  array  The sanitised row
	 */
	protected function sanitiseRow($table, $row)
	{
		$data = [];

		foreach ($this->keys[$table] as $key)
		{
			$data[$key] = isset($row[$key]) ? $row[$key] : null;
		}

		return $data;
	}

	/**
	 * Updates an entity in the storage
	 *
	 * @param   string  $table       The name of the table
	 * @param   array   $row         The data row
	 * @param   array   $identifier  key-value pairs to identify the record(s)
	 *
	 * @return  void
	 */
	public function update($table, $row, $identifier = [])
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		$data = array_filter($this->sanitiseRow($table, $row));

		if (empty($identifier) && isset($data['id']))
		{
			$identifier = ['id' => $data['id']];
		}

		$rows = [];

		foreach ($this->rows[$table] as $key => $row)
		{
			if ($this->match($row, $identifier))
			{
				$rows[] = $key;
			}
		}

		if (empty($rows))
		{
			throw new \RuntimeException("No matching entry found in {$table}.csv");
		}

		foreach ($rows as $key)
		{
			foreach ($data as $field => $value)
			{
				$this->rows[$table][$key][$field] = $value;
			}
		}
	}

	/**
	 * Checks if a row matches an identifier
	 *
	 * @param   array  $row         The row to check
	 * @param   array  $identifier  List of key-value pairs
	 *
	 * @return  boolean
	 */
	protected function match($row, $identifier)
	{
		foreach ($identifier as $key => $value)
		{
			if ($row[$key] != $value)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Deletes an entity from the storage
	 *
	 * @param   string  $table       The name of the table
	 * @param   array   $row         The data row
	 * @param   array   $identifier  key-value pairs to identify the record(s)
	 *
	 * @return  void
	 */
	public function delete($table, $row, $identifier = [])
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		if (empty($identifier))
		{
			$identifier = $this->sanitiseRow($table, $row);
		}

		$rows = [];

		foreach ($this->rows[$table] as $key => $row)
		{
			if ($this->match($row, $identifier))
			{
				$rows[] = $key;
			}
		}

		if (empty($rows))
		{
			throw new \RuntimeException("No matching entry found in {$table}.csv");
		}

		foreach ($rows as $key)
		{
			unset($this->rows[$table][$key]);
		}
	}

	/**
	 * Starts a transaction
	 *
	 * @return  void
	 */
	public function beginTransaction()
	{
		$this->snapshot = $this->rows;
	}

	/**
	 * Commit all changes
	 *
	 * @return  void
	 * @throws  \RuntimeException  on failure
	 */
	public function commit()
	{
		try
		{
			$this->storeAllTables();

			$this->snapshot = [];
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException('Commit failed: ' . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Stores all tables
	 *
	 * @return  void
	 */
	protected function storeAllTables()
	{
		foreach (array_keys($this->rows) as $table)
		{
			$this->storeTable($table);
		}
	}

	/**
	 * Persists all changes
	 *
	 * @param   string  $table  The name of the table
	 *
	 * @return  void
	 */
	protected function storeTable($table)
	{
		$fh = fopen($this->dataFile($table), 'w');

		fputcsv($fh, $this->keys[$table]);

		foreach ($this->rows[$table] as $row)
		{
			fputcsv($fh, $this->sanitiseRow($table, $row));
		}

		fclose($fh);
	}

	/**
	 * Roll back the changes
	 *
	 * @return  void
	 * @throws  \RuntimeException  on failure
	 */
	public function rollBack()
	{
		try
		{
			$this->rows = $this->snapshot;

			$this->storeAllTables();

			$this->snapshot = [];
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException('Rollback failed: ' . $e->getMessage(), 0, $e);
		}
	}
}
