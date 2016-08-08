<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Storage\Csv;

use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Storage\Csv\CsvDataGateway;
use Joomla\ORM\Storage\Csv\CsvDataMapper;
use Joomla\ORM\Storage\Csv\CsvTransactor;
use Joomla\Tests\Unit\ORM\Mocks\Article;
use Joomla\Tests\Unit\ORM\Storage\StorageTestCases;

class CsvStorageTest extends StorageTestCases
{
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/../..');

		$this->config     = parse_ini_file($dataPath . '/data/entities.csv.ini', true);
		$this->connection = new CsvDataGateway($this->config['dataPath']);
		$this->transactor = new CsvTransactor($this->connection);

		parent::setUp();

		$dataMapper = new CsvDataMapper(
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
