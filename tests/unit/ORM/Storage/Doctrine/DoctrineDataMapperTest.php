<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Joomla\ORM\Storage\Csv\CsvDataGateway;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\Tests\Unit\ORM\Mocks\Article;

class DoctrineDataMapperTest extends DataMapperTestCases
{
	/** @var  DoctrineDataMapper */
	protected $dataMapper;

	/** @var  \PHPUnit_Framework_MockObject_MockObject|Connection */
	protected $connection;

	public function setUp()
	{
		$statement = $this->createMock(Statement::class);

		$statement
			->expects($this->any())
			->method('fetchAll')
			->willReturn(
				array_values($this->articles)
			);

		$queryBuilder = $this->createMock(QueryBuilder::class);

		$queryBuilder
			->expects($this->any())
			->method('select')
			->willReturnSelf();

		$queryBuilder
			->expects($this->any())
			->method('setMaxResults')
			->willReturnSelf();

		$queryBuilder
			->expects($this->any())
			->method('execute')
			->willReturn(
				$statement
			);

		$this->connection = $this->createMock(Connection::class);

		$this->connection
			->expects($this->any())
			->method('createQueryBuilder')
			->willReturn(
				$queryBuilder
			);

		parent::setUp();

		$this->dataMapper = new DoctrineDataMapper($this->connection, Article::class, $this->builder, 'articles', $this->entityRegistry);
	}
}
