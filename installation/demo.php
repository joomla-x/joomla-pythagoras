<?php
/**
 * Script to set up a sqlite database for demonstration purposes
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../libraries/vendor/autoload.php';

$path = str_replace('sqlite:///', '', file_get_contents(__DIR__ . '/../config/database.ini'));

if (file_exists($path))
{
	unlink($path);
}

$connection = DriverManager::getConnection(['url' => 'sqlite:///' . $path]);

$data = [
	'Article' => [
			[
					'title' => 'Demo title',
					'teaser' => 'Demo teaser',
					'body' => 'Demo body',
					'author' => 'Joomla',
					'license' => 'MIT',
					'parent_id' => 0
			]
	],
	'Category' => [
			[
					'title' => 'Demo category',
					'body' => 'Demo category body',
					'parent_id' => 0
			]
	],
	'User' => [
			[
					'username' => 'demo',
					'name' => 'Demo User'
			]
	]
];

$builder = new EntityBuilder(new Locator([new RecursiveDirectoryStrategy(__DIR__ . '/../extensions')]));

foreach ($data as $entityName => $entityRows)
{
	$entity = $builder->create($entityName);

	$table = new Table(strtolower($entityName));

	foreach ($entity->asArray() as $name => $value)
	{
		if (strpos($name, '@') === 0)
		{
			continue;
		}

		$type = 'string';

		if ($name == 'id')
		{
			$type = 'integer';
		}

		$table->addColumn($name, $type);
	}

	if ($table->hasColumn('id'))
	{
		$table->setPrimaryKey(['id']);
	}

	$connection->getSchemaManager()->createTable($table);

	foreach ($entityRows as $row)
	{
		$connection->insert(strtolower($entityName), $row);
	}
}
