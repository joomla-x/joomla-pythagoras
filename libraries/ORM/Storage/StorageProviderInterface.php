<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Persistor\PersistorInterface;

/**
 * Interface StorageProviderInterface
 *
 * @package  joomla/orm
 * @since    1.0
 */
interface StorageProviderInterface
{
    /**
     * Get an EntityFinder.
     *
     * @param   string $entityName The name of the entity
     *
     * @return  EntityFinderInterface  The finder
     */
    public function getEntityFinder($entityName);

    /**
     * Get a CollectionFinder.
     *
     * @param   string $entityName The name of the entity
     *
     * @return   CollectionFinderInterface
     */
    public function getCollectionFinder($entityName);

    /**
     * Get a Persistor.
     *
     * @param   string $entityName The name of the entity
     *
     * @return  PersistorInterface
     */
    public function getPersistor($entityName);
}
