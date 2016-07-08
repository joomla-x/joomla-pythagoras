<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

use Joomla\Event\DispatcherAwareTrait;
use Joomla\ORM\Storage\Csv\CsvDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
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
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;

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
	private $config;

	/**
	 * Constructor
	 *
	 * @param   LocatorInterface $locator The XML description file locator
	 * @param   array            $config  The entity configurations
	 */
	public function __construct(LocatorInterface $locator, $config)
	{
		$this->locator = $locator;
		$this->config  = $config;
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
			$entity          = new Entity;
			$this->reflector = new EntityReflector($entity);
			$this->reflector->setDefinition($this->parseDescription($this->locateDescription($entityName), $entityName));
			$this->entities[$entityName] = $entity;
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
		$this->reflector->addHandler($basename, function () use ($relation, $locator)
		{
			$reference = $this->reflector->get($relation->name);

			if (empty($reference))
			{
				return null;
			}

			$basename = $this->getBasename($relation->name);

			// The record from {$relation->entity} with id={$field->value}
			$repository = $this->getRepository($relation->entity);
			$entity     = $repository->getById($reference);

			$this->reflector->addField(new Field([
				'name'  => $basename,
				'type'  => 'relationData',
				'value' => $entity
			]));

			return $entity;
		});
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

		$this->reflector->addHandler($basename, function () use ($relation, $locator)
		{
			$id = $this->reflector->getId();

			if (empty($id))
			{
				return null;
			}

			$basename = $this->getBasename($relation->name);

			// Records from {$relation->entity} with {$relation->reference}={$id}
			$repository = $this->getRepository($relation->entity);
			$entities   = $repository->findAll()->with($relation->reference, Operator::EQUAL, $id)->getItems();
			$this->reflector->addField(new Field([
				'name'  => $basename,
				'type'  => 'relationData',
				'value' => $entities
			]));

			return $entities;
		});
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

		$this->reflector->addHandler($basename, function () use ($relation, $locator)
		{
			$id = $relation->id ? $relation->id : $this->reflector->getId();

			if (empty($id))
			{
				return null;
			}

			$basename = $this->getBasename($relation->name);

			// The record from {$relation->entity} with {$relation->reference}={$id}
			$repository = $this->getRepository($relation->entity);
			$entity     = $repository->findOne()->with($relation->reference, Operator::EQUAL, $id)->getItem();

			$this->reflector->addField(new Field([
				'name'  => $basename,
				'type'  => 'relationData',
				'value' => $entity
			]));

			return $entity;
		});
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

		$this->reflector->addHandler($basename, function () use ($relation, $locator)
		{
			$id = $this->reflector->getId();

			if (empty($id))
			{
				return null;
			}

			$basename = $this->getBasename($relation->name);

			// Records from {$relation->entity} with {$relation->reference} IN ids from {$relation->joinTable} with {$relation->joinRef}={$id}
			$map     = $this->getRepository($relation->joinTable);
			$entries = $map->findAll()->with($relation->joinRef, Operator::EQUAL, $id)->getItems();

			$repository = $this->getRepository($relation->entity);
			$entities   = $repository->findAll()->with($relation->reference, Operator::IN, $entries->getIds());

			$this->reflector->addField(new Field([
				'name'  => $basename,
				'type'  => 'relationData',
				'value' => $entities
			]));

			return $entities;
		});
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

		$className = $this->config[$entityName]['class'];

		foreach ($matches as $match)
		{
			$entity = new $className;

			foreach ($meta->fields as $key => $definition)
			{
				// @todo Apply validation according to definition
				$entity->{$key} = $match[$key];
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
			$this->create($entityName);
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

			$this->repositories[$entityName] = new Repository($entityName, $dataMapper);
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
		$properties = get_object_vars($entity);

		foreach ($meta->relations as $type => $relations)
		{
			foreach ($relations as $field => $relation)
			{
				switch ($type)
				{
					case 'belongsTo';
						/** @var BelongsTo $relation */
						$property = $this->getBasename($relation->name);
						if (isset($properties[$property]))
						{
							$properties[$relation->name] = $properties[$property]->id;
						}
						unset($properties[$property]);
						break;

					case 'hasOne':
						/** @var HasOne $relation */
						throw new \Exception(print_r($relation, true));
						break;

					case 'hasMany':
						/** @var HasMany $relation */
						unset($properties[$relation->name]);
						break;

					case 'hasManyThrough':
						/** @var HasManyThrough $relation */
						throw new \Exception(print_r($relation, true));
						break;

					default:
						throw new OrmException("Unknown relation '$type'");
						break;
				}
			}
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
		foreach ($meta->relations as $type => $relations)
		{
			foreach ($relations as $field => $relation)
			{
				switch ($type)
				{
					case 'belongsTo';
						/** @var BelongsTo $relation */
						$repository = $this->getRepository($relation->entity);
						$property   = $this->getBasename($relation->name);
						try
						{
							$entity->{$property} = $repository->getById($entity->{$relation->name});
						}
						catch (EntityNotFoundException $e)
						{
							$entity->{$property} = null;
						}
						break;

					case 'hasOne':
						/** @var HasOne $relation */
						$repository = $this->getRepository($relation->entity);
						$property   = $relation->name;
						try
						{
							$entity->{$property} = $repository->findOne()->with($relation->reference, Operator::EQUAL, $entity->id)->getItem();
						}
						catch (EntityNotFoundException $e)
						{
							$entity->{$property} = null;
						}
						break;

					case 'hasMany':
						/** @var HasMany $relation */
						$repository = $this->getRepository($relation->entity);
						$repository->restrictTo($relation->reference, Operator::EQUAL, $entity->id);
						$property            = $relation->name;
						$entity->{$property} = $repository;
						break;

					case 'hasManyThrough':
						/** @var HasManyThrough $relation */
						throw new \Exception(print_r($relation, true));
						break;

					default:
						throw new OrmException("Unknown relation '$type'");
						break;
				}
			}
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
}
