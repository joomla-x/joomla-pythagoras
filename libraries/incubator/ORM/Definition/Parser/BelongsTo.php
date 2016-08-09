<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class BelongsTo
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class BelongsTo extends Relation
{
	/**
	 * Gets the property name for the foreign key
	 *
	 * @return  string
	 */
	public function varIdName()
	{
		return $this->propertyName($this->name);
	}

	/**
	 * Gets the property name for the entity
	 *
	 * @return  string
	 */
	public function varObjectName()
	{
		return $this->propertyName($this->getBasename($this->name));
	}

	/**
	 * Gets the column name for the foreign key
	 *
	 * @return  string
	 */
	public function colIdName()
	{
		return $this->columnName($this->name);
	}
}
