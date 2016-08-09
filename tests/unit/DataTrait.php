<?php
namespace Joomla\Tests\Unit;

use Doctrine\DBAL\DriverManager;

trait DataTrait
{
	protected function restoreData($tables = [])
	{
		$dataDir  = realpath(__DIR__ . '/ORM/data');
		$database = $dataDir . '/sqlite.test.db';

		$connection = DriverManager::getConnection(['url' => 'sqlite:///' . $database]);

		$files = glob($dataDir . '/original/*.csv');

		foreach ($files as $file)
		{
			$tableName = basename($file, '.csv');

			if (!empty($tables) && !in_array($tableName, $tables))
			{
				continue;
			}

			$csvFilename = $dataDir . '/' . $tableName . '.csv';
			unlink($csvFilename);
			copy($file, $csvFilename);

			$records = $this->loadData($file);

			$connection->beginTransaction();

			$connection->query('DELETE FROM ' . $tableName);
			foreach ($records as $record)
			{
				$connection->insert($tableName, $record);
			}
			$connection->commit();
		}
	}

	/**
	 * Load the data from the file
	 *
	 * @return  array
	 */
	protected function loadData($dataFile)
	{
		static $data = [];

		if (!isset($data[$dataFile]))
		{
			$data[$dataFile] = [];

			$fh   = fopen($dataFile, 'r');
			$keys = fgetcsv($fh);

			while (!feof($fh))
			{
				$row = fgetcsv($fh);

				if ($row === false)
				{
					break;
				}

				$data[$dataFile][] = array_combine($keys, $row);
			}

			fclose($fh);
		}

		return $data[$dataFile];
	}
}
