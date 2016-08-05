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
	private $gateway;

	/**
	 * @return CsvDataGateway
	 */
	protected function onBeforeSetup()
	{
		$dataPath = realpath(__DIR__ . '/../..');

		$this->config = parse_ini_file($dataPath . '/data/entities.csv.ini', true);

		$this->gateway          = new CsvDataGateway($this->config['dataPath']);
		$this->transactor = new CsvTransactor($this->gateway);
	}

	protected function onAfterSetUp()
	{
		$entities = [Master::class, Detail::class, Extra::class, Tag::class];

		foreach ($entities as $className)
		{
			$dataMapper             = new CsvDataMapper(
				$this->gateway,
				$className,
				basename($this->config[$className]['data'], '.csv'),
				$this->entityRegistry
			);
			$this->repo[$className] = new Repository($className, $dataMapper, $this->unitOfWork);
		}
	}
}