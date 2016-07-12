<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineTransactor;
use Joomla\Tests\Unit\ORM\Mocks\Detail;
use Joomla\Tests\Unit\ORM\Mocks\Extra;
use Joomla\Tests\Unit\ORM\Mocks\Master;
use Joomla\Tests\Unit\ORM\Mocks\Tag;
use Joomla\Tests\Unit\ORM\Storage\RelationTestCases;

class DoctrineRelationTest extends RelationTestCases
{
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/../..');

		$this->config = parse_ini_file($dataPath . '/data/entities.doctrine.ini', true);

		$connection       = DriverManager::getConnection(['url' => $this->config['databaseUrl']]);
		$this->transactor = new DoctrineTransactor($connection);

		parent::setUp();

		$entities = [Master::class, Detail::class, Extra::class, Tag::class];

		foreach ($entities as $className)
		{
			$dataMapper             = new DoctrineDataMapper(
				$connection,
				$className,
				$this->builder,
				$this->config[$className]['table'],
				$this->entityRegistry
			);
			$this->repo[$className] = new Repository($className, $dataMapper, $this->unitOfWork);
		}
	}

	public function tearDown()
	{
	}
}
