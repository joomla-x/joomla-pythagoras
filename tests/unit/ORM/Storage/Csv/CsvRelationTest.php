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
use Joomla\Tests\Unit\ORM\Mocks\Detail;
use Joomla\Tests\Unit\ORM\Mocks\Extra;
use Joomla\Tests\Unit\ORM\Mocks\Master;
use Joomla\Tests\Unit\ORM\Mocks\Tag;
use Joomla\Tests\Unit\ORM\Storage\RelationTestCases;

class CsvRelationTest extends RelationTestCases
{
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/../..');

		$this->config = parse_ini_file($dataPath . '/data/entities.csv.ini', true);

		$gateway          = new CsvDataGateway($this->config['dataPath']);
		$this->transactor = new CsvTransactor($gateway);

		parent::setUp();

		$entities = [Master::class, Detail::class, Extra::class, Tag::class];

		foreach ($entities as $className)
		{
			$dataMapper             = new CsvDataMapper(
				$gateway,
				$className,
				$this->builder,
				basename($this->config[$className]['data'], '.csv')
			);
			$this->repo[$className] = new Repository($className, $dataMapper, $this->unitOfWork);
		}
	}

	public function tearDown()
	{
	}
}
