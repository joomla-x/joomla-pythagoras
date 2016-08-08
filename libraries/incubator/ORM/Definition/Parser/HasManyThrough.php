<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class HasManyThrough
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class HasManyThrough extends Relation
{
	/** @var  string  Name of the joining table */
	public $joinTable;

	/** @var  string  Name of the field in the joinTable with the remote id */
	public $joinRef;

	/**
	 * Gets the property name for the entity
	 *
	 * @return  string
	 */
	public function varObjectName()
	{
		return $this->propertyName($this->name);
	}

	/**
	 * Gets the property name for the reference
	 *
	 * @return  string
	 */
	public function varReferenceName()
	{
		return $this->propertyName($this->reference);
	}

	/**
	 * Gets the property name for the join reference
	 *
	 * @return  string
	 */
	public function varJoinName()
	{
		return $this->propertyName($this->joinRef);
	}

	/**
	 * Gets the column name for the reference
	 *
	 * @return  string
	 */
	public function colReferenceName()
	{
		return $this->columnName($this->reference);
	}

	/**
	 * Gets the column name for the join reference
	 *
	 * @return  string
	 */
	public function colJoinName()
	{
		return $this->columnName($this->joinRef);
	}
}
