<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Finder;

use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Exception\EntityNotFoundException;

/**
 * Interface EntityFinderInterface
 *
 * @package  Joomla/orm
 * @since    1.0
 */
interface EntityFinderInterface
{
	/**
	 * Define the columns to be retrieved.
	 *
	 * @param   array $columns The column names
	 *
	 * @return  EntityFinderInterface  $this for chaining
	 */
	public function columns($columns);

	/**
	 * Define a condition.
	 *
	 * @param   mixed  $lValue The left value for the comparision
	 * @param   string $op     The comparision operator, one of the \Joomla\ORM\Finder\Operator constants
	 * @param   mixed  $rValue The right value for the comparision
	 *
	 * @return  EntityFinderInterface  $this for chaining
	 */
	public function with($lValue, $op, $rValue);

	/**
	 * Fetch the entity
	 *
	 * @return  EntityInterface
	 *
	 * @throws  EntityNotFoundException  if the specified entity does not exist.
	 */
	public function get();
}
