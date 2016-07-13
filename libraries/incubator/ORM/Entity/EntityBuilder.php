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
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;
use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Definition\Parser\HasManyThrough;
use Joomla\ORM\Definition\Parser\HasOne;
use Joomla\ORM\Definition\Parser\XmlParser;
use Joomla\ORM\Event\AfterCreateDefinitionEvent;
use Joomla\ORM\Exception\EntityNotDefinedException;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\FileNotFoundException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Service\RepositoryFactory;
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

	/** @var  RepositoryFactory */
	private $repositoryFactory;

	/** @var  EntityReflector  Reflector to manipulate the entity */
	private $reflector;

	/** @var  array */
	private $config = [];

	/** @var  IdAccessorRegistry */
	private $idAccessorRegistry;

	/** @var array */
	private $alias = [];

	/**
	 * Constructor
	 *
	 * @param   LocatorInterface   $locator            The XML description file locator
	 * @param   array              $config             The entity configurations
	 * @param   IdAccessorRegistry $idAccessorRegistry The id accessor registry
	 * @param   RepositoryFactory  $repositoryFactory  The repository factory
	 */
	public function __construct(LocatorInterface $locator, array $config, IdAccessorRegistry $idAccessorRegistry, RepositoryFactory $repositoryFactory)
	{
		$this->locator            = $locator;
		$this->config             = $config;
		$this->idAccessorRegistry = $idAccessorRegistry;
		$this->repositoryFactory  = $repositoryFactory;
	}

	/**
	 * Locate the description file
	 *
	 * @param   string $entityClass The class of the entity
	 *
	 * @return  string  The definition file path
	 */
	private function locateDescription($entityClass)
	{
		if (!isset($this->config[$entityClass]))
		{
			throw new EntityNotDefinedException($entityClass);
		}

		$definitionFile = $this->config[$entityClass]['definition'];
		$filename       = $this->locator->findFile($definitionFile);

		if (!is_null($filename))
		{
			return $filename;
		}

		throw new FileNotFoundException("Unable to locate definition file '{$definitionFile}' for entity '{$entityClass}'");
	}

	/**
	 * Parse the description file
	 *
	 * @param   string $filename    The definition file path
	 * @param   string $entityClass The class of the entity
	 *
	 * @return  EntityStructure  The parsed description
	 */
	private function parseDescription($filename, $entityClass)
	{
		$parser = new XmlParser();

		$parser->open($filename);

		$definition = $parser->parse([
			'onBeforeEntity' => [$this, 'prepareEntity'],
			'onAfterField'   => [$this, 'handleField'],
		], $this->locator);

		try
		{
			$this->getDispatcher()->dispatch(new AfterCreateDefinitionEvent($entityClass, $definition, $this));
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
	 * @internal
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
	 * @internal
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
	 * Cast array to entity
	 *
	 * @param   array  $matches     The records
	 * @param   string $entityClass The entity name
	 *
	 * @return array
	 */
	public function castToEntity($matches, $entityClass)
	{
		$result = [];

		$meta = $this->getMeta($entityClass);

		$reflection = new \ReflectionClass($entityClass);

		foreach ($matches as $match)
		{
			$entity = new $entityClass;

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
	 * @param   string $entityClass The entity name
	 *
	 * @return \Joomla\ORM\Definition\Parser\Entity
	 */
	public function getMeta($entityClass)
	{
		if (!isset($this->entities[$entityClass]))
		{
			$this->readDefinition($entityClass);
		}

		return $this->entities[$entityClass]->getDefinition();
	}

	/**
	 * Gets a repository for an entity class
	 *
	 * @param   string $entityClass The entity class
	 *
	 * @return  RepositoryInterface
	 */
	public function getRepository($entityClass)
	{
		return $this->repositoryFactory->forEntity($this->resolveAlias($entityClass));
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
		$entityClass = get_class($entity);
		$meta        = $this->getMeta($entityClass);

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
		foreach ($meta->relations['belongsTo'] as $field => $relation)
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
		$entityClass = get_class($entity);
		$meta        = $this->getMeta($entityClass);
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

		if (isset($meta->relations['belongsTo']))
		{
			$this->resolveBelongsTo($meta->relations['belongsTo'], $entity, $reflection);
		}

		if (isset($meta->relations['hasOne']))
		{
			$this->resolveHasOne($meta->relations['hasOne'], $entity, $entityId);
		}

		if (isset($meta->relations['hasMany']))
		{
			$this->resolveHasMany($meta->relations['hasMany'], $entity, $entityId);
		}

		if (isset($meta->relations['hasManyThrough']))
		{
			$this->resolveHasManyThrough($meta->relations['hasManyThrough'], $entity, $entityId);
		}
	}

	/**
	 * @param $entityClass
	 */
	private function readDefinition($entityClass)
	{
		if (!class_exists($entityClass))
		{
			$alias = $entityClass;
			foreach ($this->config as $class => $config)
			{
				if (!is_array($config))
				{
					continue;
				}

				if ($alias == basename($config['definition'], '.xml'))
				{
					$entityClass         = $class;
					$this->alias[$alias] = $entityClass;
					break;
				}
			}
		}

		$entity          = new Entity;
		$this->reflector = new EntityReflector($entity);
		$filename        = $this->locateDescription($entityClass);
		$definition      = $this->parseDescription($filename, $entityClass);
		$this->reflector->setDefinition($definition);
		$this->entities[$entityClass] = $entity;

		$meta = $this->getMeta($entityClass);

		$this->alias[$meta->name] = $entityClass;

		$key = $entity->key();
		if (empty($key))
		{
			if (isset($meta->relations['belongsTo']))
			{
				foreach ($meta->relations['belongsTo'] as $relation)
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
			$entityClass,
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

			$entityClass = $this->resolveAlias($relation->entity);
			$repository  = $this->getRepository($entityClass);
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

			$entityClass = $this->resolveAlias($relation->entity);
			$repository  = $this->getRepository($entityClass);
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

			$entityClass = $this->resolveAlias($relation->entity);
			$repository  = $this->getRepository($entityClass);

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

	private function resolveAlias($alias)
	{
		while (isset($this->alias[$alias]))
		{
			$alias = $this->alias[$alias];
		}

		if (!isset($this->entities[$alias]))
		{
			$this->readDefinition($alias);
		}

		return $alias;
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

			$entityClass = $this->resolveAlias($relation->entity);
			$repository  = $this->getRepository($entityClass);

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
