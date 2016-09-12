<?php
/**
 * Part of the Joomla! Installer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Installer;

use Doctrine\DBAL\Schema\Table;
use Joomla\DI\Container;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Definition\Parser\BelongsTo;
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;
use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Definition\Parser\HasManyThrough;
use Joomla\ORM\Definition\Parser\HasOne;
use Joomla\ORM\Definition\Parser\Relation;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Service\StorageServiceProvider;
use Joomla\String\Inflector;
use Joomla\String\Normalise;

/**
 * Class Installer
 *
 * @package  Joomla\Cms\Installer
 *
 * @since    __DEPLOY_VERSION__
 */
class Installer
{
	/** @var EntityBuilder The entity builder */
	private $builder;

	/** @var Container The container */
	private $container;

	/** @var  string The data directory */
	private $dataDirectory;

	/** @var  RepositoryFactory The repository factory */
	private $repositoryFactory;

	/** @var EntityStructure[] The entity structures */
	private $entityDefinitions = [];

	/** @var Inflector The inflector */
	private $inflector;

	/**
	 * Installer constructor.
	 *
	 * @param   string $dataDirectory The data directory
	 */
	public function __construct($dataDirectory)
	{
		$this->container     = new Container;
		$this->dataDirectory = $dataDirectory;

		$storage = new StorageServiceProvider;
		$storage->register($this->container);

		$this->repositoryFactory = $this->container->get('Repository');
		$this->builder           = $this->repositoryFactory->getEntityBuilder();
		$this->inflector         = Inflector::getInstance();

		$this->loadExistingEntities();
	}

	/**
	 * Installs an extension
	 *
	 * @param   string $source The path to the extension
	 *
	 * @return  void
	 */
	public function install($source)
	{
		$xmlDirectory = $source . '/entities';
		$strategy     = new RecursiveDirectoryStrategy($xmlDirectory);
		$this->builder->addLocatorStrategy($strategy);
		$entityNames = $this->importDefinition($xmlDirectory . '/*.xml');

		$csvDirectory = $source . '/data';

		foreach ($entityNames as $entityName)
		{
			$this->createTable($entityName);
		}

		foreach ($entityNames as $entityName)
		{
			$this->importInitialData($entityName, $csvDirectory);
		}

		$this->repositoryFactory->getUnitOfWork()->commit();
	}

	/**
	 * Import data, if present
	 *
	 * @param   string $entityName   The name of the entity
	 * @param   string $csvDirectory The directory containing the sample data
	 *
	 * @return  void
	 */
	private function importInitialData($entityName, $csvDirectory)
	{
		$tableName = $this->entityDefinitions[$entityName]->storage['table'];
		$dataFile  = $csvDirectory . '/' . $tableName . '.csv';

		if (!file_exists($dataFile))
		{
			return;
		}

		$entityClass = $this->entityDefinitions[$entityName]->class;
		$repo        = $this->repositoryFactory->forEntity($entityClass);

		foreach ($this->loadData($dataFile) as $row)
		{
			$entity = $repo->createFromArray($row);
			$repo->add($entity);
		}
	}

	/**
	 * Load the data from the file
	 *
	 * @param   string $dataFile A filename
	 *
	 * @return  array   The data
	 */
	private function loadData($dataFile)
	{
		$fh   = fopen($dataFile, 'r');
		$keys = fgetcsv($fh);

		$rows = [];

		while (!feof($fh))
		{
			$row = fgetcsv($fh);

			if ($row === false)
			{
				break;
			}

			$rows[] = array_combine($keys, $row);
		}

		fclose($fh);

		return $rows;
	}

	/**
	 * Finishes the installation
	 *
	 * @return  void
	 */
	public function finish()
	{
		// Resolve all counter-relations
		foreach ($this->entityDefinitions as $definition)
		{
			$this->resolveBelongsTo($definition);
			$this->resolveHasOnOrMany($definition);
			$this->resolveHasManyThrough($definition);
		}

		// Store XML files in a central place
		foreach ($this->entityDefinitions as $definition)
		{
			$definition->writeXml($this->dataDirectory . "/entities/{$definition->name}.xml");
		}
	}

	/**
	 * @return  void
	 */
	private function loadExistingEntities()
	{
		$this->importDefinition($this->dataDirectory . '/entities/*.xml');
	}

	/**
	 * @param   string $pattern A filename pattern
	 *
	 * @return  string[]  A list of file names
	 */
	private function findFiles($pattern)
	{
		return glob($pattern);
	}

	/**
	 * @param   string $pattern A filename pattern
	 *
	 * @return  string[]  A list of entity names
	 */
	private function importDefinition($pattern)
	{
		$entityNames = [];

		foreach ($this->findFiles($pattern) as $file)
		{
			$basename   = basename($file, '.xml');
			$definition = $this->builder->getMeta($basename);

			if (!empty($this->entityDefinitions[$basename]))
			{
				if ($this->entityDefinitions[$basename]->class == $definition->class)
				{
					// Might be an update
					continue;
				}

				throw new \RuntimeException("Another definition for $basename already exists!");
			}

			$this->entityDefinitions[$basename] = $definition;
			$entityNames[]                      = $basename;
		}

		return $entityNames;
	}

	/**
	 * @param   string $entityName The name of the entity
	 *
	 * @return  void
	 */
	private function createTable($entityName)
	{
		$prefix        = '';
		$schemaManager = $this->repositoryFactory->getSchemaManager();

		if ($schemaManager !== null)
		{
			$meta      = $this->entityDefinitions[$entityName];
			$tableName = $prefix . $meta->storage['table'];
			$table     = new Table($tableName);

			foreach (array_merge($meta->fields, $meta->relations['belongsTo']) as $field)
			{
				// @todo add proper type handling
				$type = 'string';

				if (in_array($field->type, ['int', 'integer', 'id']))
				{
					$type = 'integer';
				}

				$table->addColumn($field->columnName($field->name), $type);
			}

			$primary = explode(',', $meta->primary);
			$table->setPrimaryKey($primary);

			$schemaManager->createTable($table);
		}
	}

	/**
	 * @param   string $word A word
	 *
	 * @return  string
	 */
	private function normalise($word)
	{
		return Normalise::toUnderscoreSeparated(strtolower(Normalise::fromCamelCase($word)));
	}

	/**
	 * @param   EntityStructure $definition The entity structure
	 *
	 * @return  void
	 */
	private function resolveBelongsTo($definition)
	{
		foreach ($definition->relations['belongsTo'] as $relation)
		{
			$counterEntity    = $relation->entity;
			$counterMeta      = $this->entityDefinitions[$counterEntity];
			$counterRelations = $counterMeta->relations;

			foreach ($counterRelations['hasOne'] as $counterRelation)
			{
				if ($counterRelation->entity == $definition->name && $counterRelation->reference == $relation->name)
				{
					break 2;
				}
			}

			foreach ($counterRelations['hasMany'] as $counterRelation)
			{
				if ($counterRelation->entity == $definition->name && $counterRelation->reference == $relation->name)
				{
					break 2;
				}
			}

			// No existing counter-relation found; create it.
			$counterRelation                                     = new HasMany(
				[
					'name'      => $this->normalise($this->inflector->toPlural($definition->name)),
					'entity'    => $definition->name,
					'reference' => $relation->name,
				]
			);
			$counterRelations['hasMany'][$counterRelation->name] = $counterRelation;
		}
	}

	/**
	 * @param   EntityStructure $definition The entity structure
	 *
	 * @return  void
	 */
	private function resolveHasOnOrMany($definition)
	{
		foreach (array_merge($definition->relations['hasMany'], $definition->relations['hasOne']) as $relation)
		{
			$counterEntity    = $relation->entity;
			$counterMeta      = $this->entityDefinitions[$counterEntity];
			$counterRelations = $counterMeta->relations;

			foreach ($counterRelations['belongsTo'] as $counterRelation)
			{
				if ($counterRelation->entity == $definition->name && $counterRelation->name == $relation->reference)
				{
					break 2;
				}
			}

			// No existing counter-relation found; create it.
			$counterRelation                                       = new BelongsTo(
				[
					'name'   => $this->normalise($definition->name),
					'entity' => $definition->name,
				]
			);
			$counterRelations['belongsTo'][$counterRelation->name] = $counterRelation;
		}
	}

	/**
	 * @param   EntityStructure $definition The entity structure
	 *
	 * @return  void
	 */
	private function resolveHasManyThrough($definition)
	{
		foreach ($definition->relations['hasManyThrough'] as $relation)
		{
			// @todo Implement HasManyThrough handling
		}
	}
}
