<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Fields
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class Fields extends Element
{
	/** @var  Field[]  The field list */
	public $fields = [];

	/**
	 * Set the fields
	 *
	 * @param   Field[] $values The fields
	 *
	 * @return  void
	 */
	protected function setField($values)
	{
		foreach ($values as $name => $field)
		{
			$this->fields[$name] = $field;
		}
	}

	/**
	 * Set the fieldsets
	 *
	 * @param   Fieldset[] $values The fieldsets
	 *
	 * @return  void
	 */
	protected function setFieldset($values)
	{
		foreach ($values as $name => $field)
		{
			$this->fields[$name] = $field;
		}
	}
}
