<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineTransactor;
use Joomla\Tests\Unit\ORM\Mocks\Article;
use Joomla\Tests\Unit\ORM\Storage\StorageTestCases;

class DoctrineStorageTest extends StorageTestCases
{
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/../..');

		$this->config = parse_ini_file($dataPath . '/data/entities.doctrine.ini', true);

		$this->connection = DriverManager::getConnection(['url' => $this->config['databaseUrl']]);
		$this->transactor = new DoctrineTransactor($this->connection);

		parent::setUp();

		$dataMapper = new DoctrineDataMapper(
			$this->connection,
			Article::class,
			'articles',
			$this->entityRegistry
		);
		$this->repo = new Repository(Article::class, $dataMapper, $this->unitOfWork);
	}

	public function tearDown()
	{
	}
}
