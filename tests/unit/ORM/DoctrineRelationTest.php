<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM;

use Joomla\ORM\DataMapper\DoctrineDataMapper;
use Joomla\ORM\Repository\Repository;

class DoctrineRelationTest extends RelationTestCases
{
	public function setUp()
	{
		$path         = __DIR__ . '/data';
		$this->config = parse_ini_file($path . '/entities.doctrine.ini', true);

		parent::setUp();

		$entities = ['Master', 'Detail', 'Extra', 'Tag'];

		foreach ($entities as $entityName)
		{
			$dataMapper              = new DoctrineDataMapper(
				$entityName,
				$path . '/' . $entityName . '.xml',
				$this->builder,
				$this->config[$entityName]['dsn'],
				$this->config[$entityName]['table']
			);
			$this->repo[$entityName] = new Repository($entityName, $dataMapper);
		}
	}

	public function tearDown()
	{
	}
}
