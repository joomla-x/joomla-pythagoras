<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Driver\Statement;

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
		$statement = $this->getMockBuilder(Statement::class)->getMock();
		$statement->method('fetchAll')->willReturn($data);
		$builder = $this->getMockBuilder(QueryBuilder::class)
			->disableOriginalConstructor()
			->getMock();
		$builder->method('execute')->willReturn($statement);
		$connection = $this->getMockBuilder(Connection::class)
			->disableOriginalConstructor()
			->getMock();
		$connection->method('createQueryBuilder')->willReturn($builder);

		return $connection;
	}
}
