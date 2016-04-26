<?php
namespace Joomla\Tests\Functional\ORM\Storage\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Driver\Connection;

class BasicDoctrineTestCase extends \PHPUnit_Framework_TestCase
{

	/**
	 * Creates an in memory sqlite connection for the given data.
	 * A table is created with the column names from the given data.
	 * The data must be an array of arrays with the keys 'column' and 'value'
	 * like::
	 *
	 * [ ['foo' => 'bar] ]
	 *
	 * @param array $data
	 * @param string $tableName
	 * @return Connection
	 */
	protected function createConnection($data = [], $tableName = 'test')
	{
		$connection = DriverManager::getConnection([
				'url' => 'sqlite::memory:'
		]);

		$columns = [];
		foreach ($data as $row)
		{
			foreach ($row as $column => $value)
			{
				$columns[$column] = $column;
			}
		}

		$table = new Table($tableName);
		foreach ($columns as $col)
		{
			$table->addColumn($col, 'string');
		}
		$connection->getSchemaManager()->createTable($table);

		foreach ($data as $row)
		{
			$connection->insert($tableName, $row);
		}

		return $connection;
	}
}
