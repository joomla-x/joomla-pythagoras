<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

use Joomla\Event\DispatcherAwareTrait;
use Joomla\ORM\Definition\Locator\LocatorInterface;
use Joomla\ORM\Definition\Parser\BelongsTo;
use Joomla\ORM\Definition\Parser\Element;
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;
use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Definition\Parser\HasManyThrough;
use Joomla\ORM\Definition\Parser\HasOne;
use Joomla\ORM\Definition\Parser\XmlParser;
use Joomla\ORM\Event\AfterCreateDefinitionEvent;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\FileNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Storage\Csv\CsvDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\String\Normalise;

/**
 * Class EntityBuilder
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class EntityBuilder
{
	use DispatcherAwareTrait;

	/** @var  LocatorInterface  The XML definition file locator */
	private $locator;

	/** @var  string  Prefix for language file keys */
	private $prefix;

	/** @var  EntityInterface[]  Entities */
	private $entities;

	/** @var  RepositoryInterface[] */
	private $repositories;

	/** @var  EntityReflector  Reflector to manipulate the entity */
	private $reflector;

	/** @var  array */
	private $config = [];

	/** @var  IdAccessorRegistry */
	private $idAccessorRegistry;

	/**
	 * Constructor
	 *
	 * @param   LocatorInterface $locator The XML description file locator
	 * @param   array            $config  The entity configurations
	 * @param IdAccessorRegistry $idAccessorRegistry
	 */
	public function __construct(LocatorInterface $locator, array $config, IdAccessorRegistry $idAccessorRegistry)
	{
		$this->locator            = $locator;
		$this->config             = $config;
		$this->idAccessorRegistry = $idAccessorRegistry;
	}

	/**
	 * Get a new instance of the entity.
	 *
	 * @param   string $entityName The name of the entity
	 *
	 * @return  object  An empty entity
	 */
	public function create($entityName)
	{
		if (!isset($this->entities[$entityName]))
		{
			$this->readDefinition($entityName);
		}

		$className = $this->config[$entityName]['class'];

		return new $className;
	}

	/**
	 * Locate the description file
	 *
	 * @param   string $entityName The name of the entity
	 *
	 * @return  string  The definition file path
	 */
	private function locateDescription($entityName)
	{
		$search = $entityName . '.xml';

		if (isset($this->config[$entityName]['definition']))
		{
			$search = $this->config[$entityName]['definition'];
		}

		$filename = $this->locator->findFile($search);

		if (!is_null($filename))
		{
			return $filename;
		}

		throw new FileNotFoundException("Unable to locate definition file for entity '{$entityName}'");
	}

	/**
	 * Parse the description file
	 *
	 * @param   string $filename   The definition file path
	 * @param   string $entityName The name of the entity
	 *
	 * @return  EntityStructure  The parsed description
	 */
	private function parseDescription($filename, $entityName)
	{
		$parser = new XmlParser();

		$parser->open($filename);

		$definition = $parser->parse([
			'onBeforeEntity'        => [$this, 'prepareEntity'],
			'onAfterField'          => [$this, 'handleField'],
			'onAfterBelongsTo'      => [$this, 'handleBelongsTo'],
			'onAfterHasOne'         => [$this, 'handleHasOne'],
			'onAfterHasMany'        => [$this, 'handleHasMany'],
			'onAfterHasManyThrough' => [$this, 'handleHasManyThrough'],
			'onAfterStorage'        => [$this, 'handleStorage'],
		], $this->locator);

		try
		{
			$this->getDispatcher()->dispatch(new AfterCreateDefinitionEvent($entityName, $definition, $this));
		}
		catch (\UnexpectedValueException $e)
		{
			// Dispatcher is not set, ignoring the exception
		}

		return $definition;
	}

	/**
	 * Parser callback for onBeforeEntity event
	 *
	 * @param   array $attributes The element attributes
	 *
	 * @return  void
	 */
	public function prepareEntity($attributes)
	{
		$this->prefix = 'COM_' . strtoupper($attributes['name']) . '_FIELD_';
	}

	/**
	 * Parser callback for onAfterField event
	 *
	 * @param   Field $field The data structure
	 *
	 * @return  void
	 */
	public function handleField(Field $field)
	{
		$prefix = $this->prefix . strtoupper($field->name);

		if (!isset($field->label))
		{
			$field->label = $prefix . '_LABEL';
		}

		if (preg_match('~^([A-Z_]+)$~', $field->label, $match))
		{
			$prefix = $match[1];
		}

		if (preg_match('~^([A-Z_]+?)_LABEL$~', $field->label, $match))
		{
			$prefix = $match[1];
		}

		if (!isset($field->description))
		{
			$field->description = $prefix . '_DESC';
		}

		if (preg_match('~^([A-Z_]+?)_DESC$~', $field->description, $match))
		{
			$prefix = $match[1];
		}

		if (!isset($field->hint))
		{
			$field->hint = $prefix . '_HINT';
		}

		$this->reflector->addField($field);
	}

	/**
	 * Determine the basename of an id field
	 *
	 * @param   string $name The field name
	 *
	 * @return  string  The name without 'id' suffix
	 */
	private function getBasename($name)
	{
		return preg_replace('~^(.*?)_?id$~i', '\1', $name);
	}

	/**
	 * Parser callback for onAfterBelongsTo event
	 *
	 * @param   BelongsTo        $relation The data structure
	 * @param   LocatorInterface $locator  The XML description file locator
	 *
	 * @return  void
	 */
	public function handleBelongsTo(BelongsTo $relation, LocatorInterface $locator)
	{
		$basename = $this->getBasename($relation->name);

		$field       = new Field(get_object_vars($relation));
		$field->name = $basename . '_id';
		$field->type = 'relationKey';

		$this->reflector->addField($field);
	}

	/**
	 * Parser callback for onAfterHasMany event
	 *
	 * @param   HasMany          $relation The data structure
	 * @param   LocatorInterface $locator  The XML description file locator
	 *
	 * @return  void
	 */
	public function handleHasMany(HasMany $relation, LocatorInterface $locator)
	{
		$basename = $this->getBasename($relation->name);
	}

	/**
	 * Parser callback for onAfterHasOne event
	 *
	 * @param   HasOne           $relation The data structure
	 * @param   LocatorInterface $locator  The XML description file locator
	 *
	 * @return  void
	 */
	public function handleHasOne(HasOne $relation, LocatorInterface $locator)
	{
		$basename = $this->getBasename($relation->name);
	}

	/**
	 * Parser callback for onAfterHasManyThrough event
	 *
	 * @param   HasManyThrough   $relation The data structure
	 * @param   LocatorInterface $locator  The XML description file locator
	 *
	 * @return  void
	 */
	public function handleHasManyThrough(HasManyThrough $relation, LocatorInterface $locator)
	{
		$basename = $this->getBasename($relation->name);
	}

	public function handleStorage(Element $storage)
	{
	}

	/**
	 * Cast array to entity
	 *
	 * @param   array  $matches    The records
	 * @param   string $entityName The entity name
	 *
	 * @return array
	 */
	public function castToEntity($matches, $entityName)
	{
		$result = [];

		$meta = $this->getMeta($entityName);

		$className  = $this->config[$entityName]['class'];
		$reflection = new \ReflectionClass($className);

		foreach ($matches as $match)
		{
			$entity = new $className;

			foreach ($meta->fields as $key => $definition)
			{
				$varName = Normalise::toVariable($key);
				$colName = Normalise::toUnderscoreSeparated($key);

				$value = null;

				if (array_key_exists($colName, $match))
				{
					// @todo Apply validation according to definition
					$value = $match[$colName];
				}

				if ($reflection->hasProperty($varName))
				{
					$property = $reflection->getProperty($varName);
					$property->setAccessible(true);
					$property->setValue($entity, $value);
				}
				else
				{
					$entity->$varName = $value;
				}
			}

			$this->resolveRelations($entity, $meta);

			$result[] = $entity;
			unset($entity);
		}

		return $result;
	}

	/**
	 * Get the meta data for the entity type
	 *
	 * @param   string $entityName The entity name
	 *
	 * @return EntityInterface
	 */
	private function getMeta($entityName)
	{
		if (!isset($this->entities[$entityName]))
		{
			$this->readDefinition($entityName);
		}

		return $this->entities[$entityName]->getDefinition();
	}

	public function getRepository($entityName)
	{
		if (!isset($this->repositories[$entityName]))
		{
			$dataMapperClass = $this->config[$entityName]['dataMapper'];
			switch ($dataMapperClass)
			{
				case CsvDataMapper::class:
					$dataMapper = new CsvDataMapper(
						$entityName,
						$this->config[$entityName]['definition'],
						$this,
						$this->config[$entityName]['data']
					);
					break;

				case DoctrineDataMapper::class:
					$dataMapper = new DoctrineDataMapper(
						$entityName,
						$this->config[$entityName]['definition'],
						$this,
						$this->config[$entityName]['dsn'],
						$this->config[$entityName]['table']
					);
					break;

				default:
					throw new OrmException("Unknown data mapper '$dataMapperClass'");
					break;
			}

			$this->repositories[$entityName] = new Repository($entityName, $dataMapper, $this->idAccessorRegistry);
		}

		return $this->repositories[$entityName];
	}

	/**
	 * Reduce relations
	 *
	 * @param object $entity
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function reduce($entity)
	{
		$entityName = $this->getEntityName($entity);
		$meta       = $this->getMeta($entityName);

		$reflection = new \ReflectionClass($entity);
		$properties = [];

		foreach ($meta->fields as $name => $field)
		{
			$varName = Normalise::toVariable($field->name);
			$colName = Normalise::toUnderscoreSeparated($field->name);

			$value = null;

			if ($reflection->hasProperty($varName))
			{
				$property = $reflection->getProperty($varName);
				$property->setAccessible(true);
				$value = $property->getValue($entity);
			}

			$properties[$colName] = $value;
		}

		// Only belongsTo relations can have data in this entity
		foreach ($meta->relations->belongsTo as $field => $relation)
		{
			$colIdName  = Normalise::toUnderscoreSeparated($relation->name);
			$varObjName = Normalise::toVariable($this->getBasename($relation->name));

			$value = null;

			if ($reflection->hasProperty($varObjName))
			{
				$property = $reflection->getProperty($varObjName);
				$property->setAccessible(true);
				$object = $property->getValue($entity);
				$id     = $this->idAccessorRegistry->getEntityId($object);
				$value  = $id;
			}

			$properties[$colIdName] = $value;
		}

		return $properties;
	}

	/**
	 * Resolve relations
	 *
	 * @param object $entity
	 *
	 * @return void
	 */
	public function resolve($entity)
	{
		$entityName = $this->getEntityName($entity);
		$meta       = $this->getMeta($entityName);
		$this->resolveRelations($entity, $meta);
	}

	/**
	 * @param $entity
	 * @param $meta
	 *
	 * @throws \Exception
	 */
	private function resolveRelations($entity, $meta)
	{
		$reflection = new \ReflectionClass($entity);
		$entityId   = $this->idAccessorRegistry->getEntityId($entity);

		if (isset($meta->relations->belongsTo))
		{
			$this->resolveBelongsTo($meta->relations->belongsTo, $entity, $reflection);
		}

		if (isset($meta->relations->hasOne))
		{
			$this->resolveHasOne($meta->relations->hasOne, $entity, $entityId);
		}

		if (isset($meta->relations->hasMany))
		{
			$this->resolveHasMany($meta->relations->hasMany, $entity, $entityId);
		}

		if (isset($meta->relations->hasManyThrough))
		{
			$this->resolveHasManyThrough($meta->relations->hasManyThrough, $entity, $entityId);
		}
	}

	/**
	 * @param object $entity
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function getEntityName($entity)
	{
		foreach ($this->config as $entityName => $config)
		{
			if ($entity instanceof $config['class'])
			{
				return $entityName;
			}
		}

		throw new EntityNotFoundException("Unknown class " . get_class($entity));
	}

	/**
	 * @param $entityName
	 */
	private function readDefinition($entityName)
	{
		$entity          = new Entity;
		$this->reflector = new EntityReflector($entity);
		$this->reflector->setDefinition($this->parseDescription($this->locateDescription($entityName), $entityName));
		$this->entities[$entityName] = $entity;

		$key = $entity->key();
		if (empty($key))
		{
			$meta = $this->getMeta($entityName);
			if (isset($meta->relations->belongsTo))
			{
				foreach ($meta->relations->belongsTo as $relation)
				{
					$key = Normalise::toVariable($relation->name);
					break;
				}
			}
		}
		if (empty($key))
		{
			$key = 'id';
		}
		$this->idAccessorRegistry->registerReflectionIdAccessors(
			$this->config[$entityName]['class'],
			$key
		);
	}

	/**
	 * @param HasManyThrough[] $relations
	 * @param object           $entity
	 * @param int|string       $entityId
	 */
	private function resolveHasManyThrough($relations, $entity, $entityId)
	{
		foreach ($relations as $field => $relation)
		{
			$varObjName  = Normalise::toVariable($relation->name);
			$colRefName  = Normalise::toUnderscoreSeparated($relation->reference);
			$colJoinName = Normalise::toUnderscoreSeparated($relation->joinRef);

			$mapRepo = $this->getRepository($relation->joinTable);
			$ids     = $mapRepo
				->findAll()
				->columns($colJoinName)
				->with($colRefName, Operator::EQUAL, $entityId)
				->getItems();

			$repository = $this->getRepository($relation->entity);
			$repository->restrictTo('id', Operator::IN, $ids);

			$entity->{$varObjName} = $repository;
		}
	}

	/**
	 * @param HasMany[]  $relations
	 * @param object     $entity
	 * @param int|string $entityId
	 */
	private function resolveHasMany($relations, $entity, $entityId)
	{
		foreach ($relations as $field => $relation)
		{
			$varObjName = Normalise::toVariable($relation->name);
			$colRefName = Normalise::toUnderscoreSeparated($relation->reference);

			$repository = $this->getRepository($relation->entity);
			$repository->restrictTo($colRefName, Operator::EQUAL, $entityId);

			$entity->{$varObjName} = $repository;
		}
	}

	/**
	 * @param HasOne[]   $relations
	 * @param object     $entity
	 * @param int|string $entityId
	 */
	private function resolveHasOne($relations, $entity, $entityId)
	{
		foreach ($relations as $field => $relation)
		{
			$varObjName = Normalise::toVariable($relation->name);
			$colRefName = Normalise::toUnderscoreSeparated($relation->reference);

			$repository = $this->getRepository($relation->entity);

			try
			{
				$object = $repository
					->findOne()
					->with($colRefName, Operator::EQUAL, $entityId)
					->getItem();
			}
			catch (EntityNotFoundException $e)
			{
				$object = null;
			}

			$entity->{$varObjName} = $object;
		}
	}

	/**
	 * @param BelongsTo[]      $relations
	 * @param object           $entity
	 * @param \ReflectionClass $reflection
	 */
	private function resolveBelongsTo($relations, $entity, $reflection)
	{
		foreach ($relations as $field => $relation)
		{
			$varIdName  = Normalise::toVariable($relation->name);
			$varObjName = Normalise::toVariable($this->getBasename($relation->name));

			$repository = $this->getRepository($relation->entity);

			$property = $reflection->getProperty($varIdName);
			$property->setAccessible(true);
			$objectId = $property->getValue($entity);

			try
			{
				$object = $repository->getById($objectId);
			}
			catch (EntityNotFoundException $e)
			{
				$object = null;
			}

			$entity->{$varObjName} = $object;
		}
	}
}
