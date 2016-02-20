<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Entity
 *
 * @package  Joomla/orm
 * @since    1.0
 */
class Entity extends Element
{
	/** @var  string  Type of the entity */
	public $name;

	/** @var  string  Parent type */
	public $extends;

	/** @var Relation[]  List of relations */
	public $relations = [];

	/** @var  Field[]  List of fields */
	public $fields = [];

	/**
	 * Set the fields
	 *
	 * @param   Field[] $values The fields
	 *
	 * @return  void
	 */
	protected function setFields($values)
	{
		foreach ($values[0]->fields as $name => $field)
		{
			$this->fields[$name] = $field;
		}
	}

	/**
	 * Set the relations
	 *
	 * @param   Relation[] $values The relations
	 *
	 * @return  void
	 */
	protected function setRelations($values)
	{
		$this->relations = $values[0];
	}
}
