<?php
namespace Joomla\Tests\Unit\ORM;

use Joomla\ORM\DataMapper\DoctrineDataMapper;
use Joomla\ORM\Repository\Repository;

class DoctrineStorageTest extends StorageTestCases
{
	public function setUp()
	{
		$this->config = parse_ini_file(__DIR__ . '/data/entities.doctrine.ini', true);

		parent::setUp();

		$dataMapper = new DoctrineDataMapper(
			'Article',
			'Article.xml',
			$this->builder,
			'sqlite:///' . __DIR__ . '/data/sqlite.test.db',
			'articles'
		);
		$this->repo = new Repository('Article', $dataMapper);
	}

	public function tearDown()
	{
	}
}
