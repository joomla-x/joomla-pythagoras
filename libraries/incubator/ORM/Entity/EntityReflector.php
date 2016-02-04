<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

use Joomla\ORM\Definition\Parser\Entity as EntityStructure;
use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Repository\StorageProviderInterface;

/**
 * Class EntityReflector
 *
 * @package  joomla/orm
 * @since    1.0
 */
class EntityReflector
{
    /** @var  EntityInterface  The entity */
    private $entity;

    /** @var \ReflectionProperty */
    private $fields;

    /** @var \ReflectionProperty */
    private $relationHandlers;

    /**
     * Constructor
     *
     * @param   Entity $entity The entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;

        $this->fields = new \ReflectionProperty('Joomla\ORM\Entity\Entity', 'fields');
        $this->fields->setAccessible(true);

        $this->relationHandlers = new \ReflectionProperty('Joomla\ORM\Entity\Entity', 'relationHandlers');
        $this->relationHandlers->setAccessible(true);
    }

    /**
     * Get the id value of an entity
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->get($this->entity->key());
    }

    /**
     * Get a value from an entity
     *
     * @param   string $property The name of the property
     *
     * @return  mixed  The value of the property
     */
    public function get($property)
    {
        return $this->entity->$property;
    }

    /**
     * Add a field to the entity
     *
     * @param   Field $field The field to add
     *
     * @return  void
     */
    public function addField(Field $field)
    {
        $tmp = $this->fields->getValue($this->entity);
        $tmp[$field->name] = $field;
        $this->fields->setValue($this->entity, $tmp);

        if ($field->type == 'id') {
            $key = new \ReflectionProperty('Joomla\ORM\Entity\Entity', 'key');
            $key->setAccessible(true);
            $key->setValue($this->entity, $field->name);
        }
    }

    /**
     * Add a relation handler to the entity
     *
     * @param   string $name Field name
     * @param   Callable $handler The relation handler
     *
     * @return  void
     */
    public function addHandler($name, $handler)
    {
        $tmp = $this->relationHandlers->getValue($this->entity);
        $tmp[$name] = $handler;
        $this->relationHandlers->setValue($this->entity, $tmp);
    }

    /**
     * Set the data definition
     *
     * @param   EntityStructure $definition The data definition
     *
     * @return  void
     */
    public function setDefinition(EntityStructure $definition)
    {
        $tmp = new \ReflectionProperty('Joomla\ORM\Entity\Entity', 'definition');
        $tmp->setAccessible(true);
        $tmp->setValue($this->entity, $definition);
    }

    /**
     * Set the storage provider
     *
     * @param   StorageProviderInterface $provider The storage provider
     *
     * @return  void
     */
    public function setStorageProvider(StorageProviderInterface $provider)
    {
        $tmp = new \ReflectionProperty('Joomla\ORM\Entity\Entity', 'storage');
        $tmp->setAccessible(true);
        $tmp->setValue($this->entity, $provider);
    }
}
