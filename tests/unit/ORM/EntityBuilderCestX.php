<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use UnitTester;

class EntityBuilderCest
{
	public function _before(UnitTester $I)
	{
	}

	public function _after(UnitTester $I)
	{
	}

	/**
	 * @testdox EntityBuilder can read XML, JSON, and YaML
	 */
	public function FileFormats(UnitTester $I)
	{
		$locator = new Locator([new RecursiveDirectoryStrategy(__DIR__ . '/data')]);
		$builder = new EntityBuilder($locator);

		$expected = [
			"@type"     => "article",
			"@key"      => "id",
			"id"        => null,
			"title"     => null,
			"teaser"    => null,
			"body"      => null,
			"author"    => null,
			"license"   => null,
			"parent_id" => null
		];

		foreach (['XmlEntity', 'JsonEntity', 'YmlEntity', 'YamlEntity'] as $entityName)
		{
			$entity = $builder->create($entityName);
			$I->assertEquals('article', $entity->type());
			$I->assertEquals($expected, $entity->asArray());
		}
	}
}
