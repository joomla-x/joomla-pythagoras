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
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class Entity extends Element
{
	/** @var  string  Type of the entity */
	public $name;

	/** @var  string  Parent type */
	public $extends;

	/** @var Relation[][]  List of relations */
	public $relations = [];

	/** @var  Field[]  List of fields */
	public $fields = [];

	/** @var array */
	public $storage = null;

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
		#throw new \Exception(print_r($values[0], true));
		if (isset($values[0]->belongsTo))
		{
			$this->relations['belongsTo'] = $values[0]->belongsTo;
		}
		if (isset($values[0]->hasOne))
		{
			$this->relations['hasOne'] = $values[0]->hasOne;
		}
		if (isset($values[0]->hasMany))
		{
			$this->relations['hasMany'] = $values[0]->hasMany;
		}
		if (isset($values[0]->hasManyThrough))
		{
			$this->relations['hasManyThrough'] = $values[0]->hasManyThrough;
		}
	}

	protected function setStorage($values)
	{
		$vars = get_object_vars($values[0]);
		foreach ($vars as $type => $attributes)
		{
			$this->storage = get_object_vars($attributes[0]);
			$this->storage['type'] = $type;
			break;
		}
	}
}
