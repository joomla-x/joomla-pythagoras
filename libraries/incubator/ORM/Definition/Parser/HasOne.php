<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class HasOne
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class HasOne extends Relation
{
	/** @var  string  The id */
	public $id = null;

	/** @var bool Whether or not to cascade deletions */
	public $cascadeDeletion = true;

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
	 * Gets the column name for the reference
	 *
	 * @return  string
	 */
	public function colReferenceName()
	{
		return $this->columnName($this->reference);
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
	 * Determines, whether the child should be deleted, when the parent is deleted
	 *
	 * @return  boolean
	 */
	public function cascadeDelete()
	{
		return $this->cascadeDeletion;
	}
}
