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

		$dataDir    = JPATH_ROOT . '/tests/unit/ORM/data';
		$database   = $dataDir . '/sqlite.test.db';
		$dataMapper = new DoctrineDataMapper(Article::class, $dataDir . '/Article.xml', 'sqlite:///' . $database, 'articles');
		$this->repo = new Repository(Article::class, $dataMapper);
	}

	public function tearDown()
	{
	}
}
