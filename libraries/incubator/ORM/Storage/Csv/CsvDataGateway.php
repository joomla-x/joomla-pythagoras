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
 * @since    1.0
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

	public function __construct($dataPath)
	{
		$this->dataPath = $dataPath;
	}

	/**
	 * Find multiple entities.
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
	 * Inserts an entity to the storage
	 */
	public function insert($table, $row)
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		$data = $this->sanitiseRow($table, $row);

		if ($this->hasId($table) && isset($this->rows[$table][$data['id']]))
		{
			throw new \RuntimeException("Id {$data['id']} already exists in {$table}.csv");
		}

		$this->rows[$table][$data['id']] = $data;
	}

	/**
	 * Updates an entity in the storage
	 */
	public function update($table, $row)
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		$data = $this->sanitiseRow($table, $row);

		if ($this->hasId($table) && isset($this->rows[$table][$data['id']]))
		{
			foreach ($data as $key => $value)
			{
				if (is_null($value))
				{
					continue;
				}

				$this->rows[$table][$data['id']][$key] = $value;
			}

			return;
		}

		throw new \RuntimeException("No entry with Id {$data['id']} found in {$table}.csv");
	}

	/**
	 * Deletes an entity from the storage
	 */
	public function delete($table, $row)
	{
		if (!array_key_exists($table, $this->rows))
		{
			$this->loadTable($table);
		}

		$data = $this->sanitiseRow($table, $row);

		if ($this->hasId($table) && isset($this->rows[$table][$data['id']]))
		{
			unset($this->rows[$table][$data['id']]);

			return;
		}

		throw new \RuntimeException("No entry with Id {$data['id']} found in {$table}.csv");
	}

	public function beginTransaction()
	{
		$this->snapshot = $this->rows;
	}

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

	/**
	 * Load the data from the file
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

			$data = array_combine($this->keys[$table], $row);

			if ($this->hasId($table))
			{
				$this->rows[$table][$data['id']] = $data;
			}
			else
			{
				$this->rows[$table][] = $data;
			}
		}

		fclose($fh);
	}

	/**
	 * Persists all changes
	 *
	 * @return void
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
	 * @param $table
	 *
	 * @return string
	 */
	protected function dataFile($table)
	{
		return $this->dataPath . '/' . $table . '.csv';
	}

	/**
	 * @param $table
	 * @param $row
	 *
	 * @return array
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

	protected function hasId($table)
	{
		return in_array('id', $this->keys[$table]);
	}

	protected function storeAllTables()
	{
		foreach (array_keys($this->rows) as $table)
		{
			$this->storeTable($table);
		}
	}
}
