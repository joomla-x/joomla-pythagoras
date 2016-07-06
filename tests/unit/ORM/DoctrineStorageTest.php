<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Joomla\ORM\DataMapper\DoctrineDataMapper;
use Joomla\ORM\Repository\Repository;
use Joomla\Tests\Unit\ORM\DatabaseTestCases;
use Joomla\Tests\Unit\ORM\TestData\Article;

class DoctrineStorageTest extends DatabaseTestCases
{
	public function setUp()
	{
		parent::setUp();

		$dataMapper = new DoctrineDataMapper(
			'Article',
			'Article.xml',
			$this->builder,
			'sqlite:///' . __DIR__ . '/data/sqlite.test.db',
			'articles'
		);
		$this->repo = new Repository(Article::class, $dataMapper);
	}

	public function tearDown()
	{
	}
}
