<?php

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

define('JPATH_ROOT', realpath(dirname(__DIR__)));

ini_set('date.timezone', 'UTC');

require_once JPATH_ROOT . '/libraries/vendor/autoload.php';

try
{
	$installer = new Installer(JPATH_ROOT . "/data");
	$installer->install(JPATH_ROOT . '/libraries/incubator/PageBuilder');
	$installer->finish();

	return 0;
}
catch (\Exception $e)
{
	echo "\n" . $e->getMessage() . "\n";

	return 1;
}

class Installer
{
	/** @var EntityBuilder */
	private $builder;

	/** @var Container */
	private $container;

	/** @var  string */
	private $dataDirectory;

	/** @var  RepositoryFactory */
	private $repositoryFactory;

	/** @var EntityStructure[] */
	private $entityDefinitions = [];

	private $inflector;

	public function __construct($dataDirectory)
	{
		$this->container     = new Container();
		$this->dataDirectory = $dataDirectory;

		$storage = new StorageServiceProvider;
		$storage->register($this->container);

		$this->repositoryFactory = $this->container->get('Repository');
		$this->builder           = $this->repositoryFactory->getEntityBuilder();
		$this->inflector         = Inflector::getInstance();

		$this->loadExistingEntities();
	}

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
	 * @param $entityName
	 * @param $csvDirectory
	 */
	private function importInitialData($entityName, $csvDirectory)
	{
		$tableName = $this->entityDefinitions[$entityName]->storage['table'];
		$dataFile  = $csvDirectory . '/' . $tableName . '.csv';

		if (!file_exists($dataFile))
		{
			return;
		}

		$repo = $this->repositoryFactory->forEntity($entityName);

		// echo "Importing data for $entityName\n";

		foreach ($this->loadData($dataFile) as $row)
		{
			// echo json_encode($row) . "\n";
			$entity = $repo->createFromArray($row);
			$repo->add($entity);
		}
	}

	/**
	 * Load the data from the file
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

	public function finish()
	{
		// Resolve all counter-relations
		foreach ($this->entityDefinitions as $definition)
		{
			// echo "\n{$definition->name}\n";
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

	private function loadExistingEntities()
	{
		$this->importDefinition($this->dataDirectory . '/entities/*.xml');
	}

	private function findFiles($pattern)
	{
		return glob($pattern);
	}

	/**
	 * @param $pattern
	 *
	 * @return array
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
				throw new \RuntimeException("Definition for $basename already exists!");
			}

			$this->entityDefinitions[$basename] = $definition;
			$entityNames[]                      = $basename;
		}

		return $entityNames;
	}

	/**
	 * @param $entityName
	 *
	 * @return void
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

				/** @var Relation|Field $field */
				$table->addColumn($field->columnName($field->name), $type);
			}

			$primary = explode(',', $meta->primary);
			$table->setPrimaryKey($primary);

			$schemaManager->createTable($table);
		}
	}

	/**
	 * @param $word
	 *
	 * @return string
	 */
	private function normalise($word)
	{
		return Normalise::toUnderscoreSeparated(strtolower(Normalise::fromCamelCase($word)));
	}

	/**
	 * @param $definition
	 *
	 * @return void
	 */
	private function resolveBelongsTo($definition)
	{
		foreach ($definition->relations['belongsTo'] as $relation)
		{
			/** @var BelongsTo $relation */
			// echo $definition->name . '::$' . $relation->name . ': ' . print_r($relation, true);
			$counterEntity    = $relation->entity;
			$counterMeta      = $this->entityDefinitions[$counterEntity];
			$counterRelations = $counterMeta->relations;

			foreach ($counterRelations['hasOne'] as $counterRelation)
			{
				/** @var HasOne $counterRelation */
				if ($counterRelation->entity == $definition->name && $counterRelation->reference == $relation->name)
				{
					// echo "Found relation: {$counterEntity} hasOne {$counterRelation->entity} in \${$counterRelation->name}\n";
					break 2;
				}
			}

			foreach ($counterRelations['hasMany'] as $counterRelation)
			{
				/** @var HasMany $counterRelation */
				if ($counterRelation->entity == $definition->name && $counterRelation->reference == $relation->name)
				{
					// echo "Found relation: {$counterEntity} hasMany {$counterRelation->entity} in \${$counterRelation->name}\n";
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
			// echo "Created relation: {$counterEntity} hasMany {$counterRelation->entity} in \${$counterRelation->name}\n";
		}
	}

	/**
	 * @param $definition
	 */
	private function resolveHasOnOrMany($definition)
	{
		foreach (array_merge($definition->relations['hasMany'], $definition->relations['hasOne']) as $relation)
		{
			/** @var HasMany $relation */
			// echo $definition->name . '::$' . $relation->name . ': ' . print_r($relation, true);
			$counterEntity    = $relation->entity;
			$counterMeta      = $this->entityDefinitions[$counterEntity];
			$counterRelations = $counterMeta->relations;

			foreach ($counterRelations['belongsTo'] as $counterRelation)
			{
				/** @var BelongsTo $counterRelation */
				if ($counterRelation->entity == $definition->name && $counterRelation->name == $relation->reference)
				{
					// echo "Found relation: {$counterEntity}::\${$counterRelation->name} belongsTo (points to) {$counterRelation->entity}\n";
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
			// echo "Created relation: {$counterEntity}::\${$counterRelation->name} belongsTo (points to) {$counterRelation->entity}\n";
		}
	}

	/**
	 * @param $definition
	 */
	private function resolveHasManyThrough($definition)
	{
		foreach ($definition->relations['hasManyThrough'] as $relation)
		{
			/** @var HasManyThrough $relation */
			// @todo Implement HasManyThrough handling
		}
	}
}
