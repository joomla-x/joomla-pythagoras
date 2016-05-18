<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

use Joomla\ORM\Definition\Locator\LocatorInterface;
use Joomla\ORM\Definition\Parser\BelongsTo;
use Joomla\ORM\Definition\Parser\Element;
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;
use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Definition\Parser\HasManyThrough;
use Joomla\ORM\Definition\Parser\HasOne;
use Joomla\ORM\Definition\Parser\JsonParser;
use Joomla\ORM\Definition\Parser\XmlParser;
use Joomla\ORM\Definition\Parser\YamlParser;
use Joomla\ORM\Exception\FileNotFoundException;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Repository\Repository;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\ORM\Event\AfterCreateDefinitionEvent;

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

	/** @var  EntityInterface  Entity under construction */
	private $entity;

	/** @var  EntityReflector  Reflector to manipulate the entity */
	private $reflector;

	/**
	 * Constructor
	 *
	 * @param   LocatorInterface $locator The XML description file locator
	 */
	public function __construct(LocatorInterface $locator)
	{
		$this->locator = $locator;
	}

	/**
	 * Get a new instance of the entity.
	 *
	 * @param   string $entityName The name of the entity
	 *
	 * @return  EntityInterface  An empty entity
	 */
	public function create($entityName)
	{
		$entity          = new Entity;
		$this->reflector = new EntityReflector($entity);

		$this->reflector->setDefinition($this->parseDescription($this->locateDescription($entityName), $entityName));

		return $entity;
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
		foreach (['.xml', '.json', '.yml', '.yaml'] as $extension)
		{
			$filename = $this->locator->findFile($entityName . $extension);

			if (!is_null($filename))
			{
				return $filename;
			}
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
		$extension = preg_replace('~^.*?\.([^.]+)$~', '\1', $filename);

		switch ($extension)
		{
			case 'xml':
				$parser = new XmlParser();
				break;

			case 'json':
				$parser = new JsonParser();
				break;

			case 'yml':
			case 'yaml':
				$parser = new YamlParser();
				break;

			default:
				throw new \RuntimeException("Unable to handle '.$extension' definition files.");
				break;
		}

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
		$this->prefix = 'COM_' . $attributes['name'] . '_FIELD_';
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
			$repository = new Repository($relation->entity, new EntityBuilder($locator));
			$entity     = $repository->findById($reference);

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
			$repository = new Repository($relation->entity, new EntityBuilder($locator));
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
			$repository = new Repository($relation->entity, new EntityBuilder($locator));
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
			$map     = new Repository($relation->joinTable, new EntityBuilder($locator));
			$entries = $map->findAll()->with($relation->joinRef, Operator::EQUAL, $id)->getItems();

			$repository = new Repository($relation->entity, $locator);
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
		foreach ($storage->toArray() as $type => $info)
		{
			$handler  = null;
			$param1   = null;
			$param2   = null;

			switch ($type)
			{
				case 'default':
					$handler = '\Joomla\ORM\Storage\DefaultProvider';
					$param1 = $info[0]->table;
					break;

				case 'api':
					$handler = $info[0]->handler;
					$param1 = $info[0]->{'base-url'};
					break;

				case 'special':
					$parts = explode('://', $info[0]->dsn);
					switch ($parts[0])
					{
						case 'csv':
							$handler = '\Joomla\ORM\Storage\CsvProvider';
							$param1   = $parts[1];
							break;
						default:
							$handler = '\Joomla\ORM\Storage\Doctrine\DoctrineProvider';
							$param1   = $info[0]->dsn;
							$param2   = $info[0]->table;
							break;
					}
					break;

				default:
					throw new \Exception("Unknown storage type ''");
					break;
			}

			$this->reflector->setStorageProvider(new $handler($param1, $this, $param2));
		}
	}
}
