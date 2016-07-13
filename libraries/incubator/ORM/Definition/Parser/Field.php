<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Field
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class Field extends Element
{
	/** @var  string  The field name */
	public $name = 'unknown';

	/** @var  string  The data type */
	public $type;

	/** @var  array  A list of validation rules */
	public $validation = [];

	/** @var  array  A list of selectable values */
	public $options = [];

	/** @var  mixed  The value of the field */
	public $value;

	public $label;

	public $description;

	public $hint;

	public $default;

	/**
	 * Set the validation rules
	 *
	 * @param   array $values The validation rules
	 *
	 * @return  void
	 */
	protected function setValidation($values)
	{
		foreach ($values as $validation)
		{
			$value = isset($validation->value) ? $validation->value : true;

			$this->validation[$validation->rule] = $value;
		}
	}

	/**
	 * Set the options
	 *
	 * @param   array $values The options
	 *
	 * @return  void
	 */
	protected function setOption($values)
	{
		foreach ($values as $option)
		{
			$this->options[$option->value] = $option->label;
		}
	}
}
