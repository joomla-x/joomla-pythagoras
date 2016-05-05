<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;

class BasicDoctrineTestCase extends \PHPUnit_Framework_TestCase
{

	/**
	 *
	 * @param array $data
	 * @return Connection
	 */
	protected function createConnection($data = [])
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
