<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM;

use Joomla\ORM\DataMapper\CsvDataMapper;
use Joomla\ORM\Repository\Repository;

class CsvRelationTest extends RelationTestCases
{
	public function setUp()
	{
		$path         = __DIR__ . '/data';
		$this->config = parse_ini_file($path . '/entities.csv.ini', true);

		parent::setUp();

		$entities = ['Master', 'Detail', 'Extra', 'Tag'];

		foreach ($entities as $entityName)
		{
			$dataMapper              = new CsvDataMapper(
				$entityName,
				$path . '/' . $entityName . '.xml',
				$this->builder,
				$this->config[$entityName]['data']
			);
			$this->repo[$entityName] = new Repository($entityName, $dataMapper);
		}
	}

	public function tearDown()
	{
	}
}
