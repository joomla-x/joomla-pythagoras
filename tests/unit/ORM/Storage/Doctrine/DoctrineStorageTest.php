<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\Tests\Unit\ORM\Storage\StorageTestCases;

class DoctrineStorageTest extends StorageTestCases
{
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/../../data');

		$this->config = parse_ini_file($dataPath . '/entities.doctrine.ini', true);

		parent::setUp();

		$dataMapper = new DoctrineDataMapper(
			'Article',
			'Article.xml',
			$this->builder,
			'sqlite:///' . $dataPath . '/sqlite.test.db',
			'articles'
		);
		$this->repo = new Repository('Article', $dataMapper);
	}

	public function tearDown()
	{
	}
}
