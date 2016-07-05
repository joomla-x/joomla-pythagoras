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
use Joomla\Tests\Unit\ORM\TestData\Article;
use UnitTester;

class CsvStorageTest extends DatabaseTestCases
{
	public function setUp()
	{
		parent::setUp();

		$dataMapper = new CsvDataMapper(Article::class, __DIR__ . '/data/Article.xml', __DIR__ . '/data/articles.csv');
		$this->repo = new Repository(Article::class, $dataMapper);
	}

	public function tearDown()
	{
	}
}
