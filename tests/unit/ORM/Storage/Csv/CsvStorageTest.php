<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Storage\Csv;

use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Storage\Csv\CsvDataMapper;
use Joomla\Tests\Unit\ORM\Storage\StorageTestCases;

class CsvStorageTest extends StorageTestCases
{
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/../../data');

		$this->config = parse_ini_file($dataPath . '/entities.csv.ini', true);

		parent::setUp();

		$dataMapper = new CsvDataMapper(
			'Article',
			'Article.xml',
			$this->builder,
			$dataPath . '/articles.csv'
		);
		$this->repo = new Repository('Article', $dataMapper);
	}

	public function tearDown()
	{
	}
}
