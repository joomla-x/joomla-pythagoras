<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Finder;

use Joomla\ORM\Collection\CollectionInterface;

/**
 * Interface CollectionFinderInterface
 *
 * @package  joomla/orm
 * @since    1.0
 */
interface CollectionFinderInterface
{
    /**
     * Define the columns to be retrieved.
     *
     * @param   array $columns The column names
     *
     * @return  CollectionFinderInterface  $this for chaining
     */
    public function columns($columns);

    /**
     * Define a condition.
     *
     * @param   mixed $lValue The left value for the comparision
     * @param   string $op The comparision operator, one of the \Joomla\ORM\Finder\Operator constants
     * @param   mixed $rValue The right value for the comparision
     *
     * @return  CollectionFinderInterface  $this for chaining
     */
    public function with($lValue, $op, $rValue);

    /**
     * Set the ordering.
     *
     * @param   string $column The name of the ordering column
     * @param   string $direction One of 'ASC' (ascending) or 'DESC' (descending)
     *
     * @return  CollectionFinderInterface  $this for chaining
     */
    public function orderBy($column, $direction = 'ASC');

    /**
     * Fetch the entity
     *
     * @param   int $count The number of matching entities to retrieve
     * @param   int $start The index of the first entity to retrieve
     *
     * @return  CollectionInterface
     */
    public function get($count = null, $start = 0);
}
