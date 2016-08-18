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
	protected function onBeforeSetup()
	{
		$dataPath = realpath(__DIR__ . '/../..');

		$this->config = parse_ini_file($dataPath . '/data/entities.doctrine.ini', true);

		$this->connection = DriverManager::getConnection(['url' => $this->config['databaseUrl']]);
		$this->transactor = new DoctrineTransactor($this->connection);
	}

	protected function onAfterSetUp()
	{
		$entities = [Master::class, Detail::class, Extra::class, Tag::class];

		foreach ($entities as $className)
		{
			$meta                   = $this->builder->getMeta($className);
			$dataMapper             = new DoctrineDataMapper(
				$this->connection,
				$className,
				$meta->storage['table'],
				$this->entityRegistry
			);
			$this->repo[$className] = new Repository($className, $dataMapper, $this->unitOfWork);
		}
	}
}
