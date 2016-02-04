<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Definition\Locator\LocatorInterface;
use Joomla\ORM\Entity\Entity;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Persistor\PersistorInterface;
use Joomla\ORM\Validator\ValidatorInterface;

/**
 * Class Repository
 *
 * @package  joomla/orm
 * @since    1.0
 */
class Repository implements RepositoryInterface
{
    /** @var  string  The name (type) of the entity */
    private $entityName;

    /** @var  Entity  Prebuilt (empty) entity */
    private $prototype = null;

    /** @var EntityBuilder */
    private $builder;

    /**
     * Constructor
     *
     * @param   string $entityName The name (type) of the entity
     * @param   LocatorInterface $locator The XML description file locator
     */
    public function __construct($entityName, LocatorInterface $locator = null)
    {
        $this->entityName = $entityName;
        $this->builder = new EntityBuilder($locator);
    }

    /**
     * Create a new entity.
     *
     * @return  EntityInterface  A new instance of the entity
     */
    public function create()
    {
        $this->buildPrototype();

        return clone $this->prototype;
    }

    /**
     * Build a prototype (once) for the entity.
     *
     * @return  void
     */
    private function buildPrototype()
    {
        if (empty($this->prototype)) {
            $this->prototype = $this->builder->create($this->entityName);
        }
    }

    /**
     * Find an entity using its id.
     *
     * findById() is a convenience method, It is equivalent to
     * ->findOne()->with('id', \Joomla\ORM\Finder\Operator::EQUAL, '$id)->get()
     *
     * @param   mixed $id The id value
     *
     * @return  EntityInterface  The requested entity
     *
     * @throws  EntityNotFoundException  if the entity does not exist
     */
    public function findById($id)
    {
        $this->buildPrototype();

        return $this->findOne()->with($this->prototype->key(), Operator::EQUAL, $id)->get();
    }

    /**
     * Find a single entity.
     *
     * @return  EntityFinderInterface  The responsible Finder object
     */
    public function findOne()
    {
        $this->buildPrototype();

        return $this->prototype->getStorage()->getEntityFinder($this->entityName);
    }

    /**
     * Find multiple entities.
     *
     * @return  CollectionFinderInterface  The responsible Finder object
     */
    public function findAll()
    {
        $this->buildPrototype();

        return $this->prototype->getStorage()->getCollectionFinder($this->entityName);
    }

    /**
     * Get the validator
     *
     * @return  ValidatorInterface
     */
    public function validator()
    {
        // TODO: Implement validator() method.
    }

    /**
     * Get the persistor
     *
     * @return  PersistorInterface
     */
    public function persistor()
    {
        $this->buildPrototype();

        return $this->prototype->getStorage()->getPersistor($this->entityName);
    }
}
