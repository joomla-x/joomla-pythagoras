<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

use Joomla\String\Normalise;

/**
 * Class Element
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class Element
{
	/**
	 * Constructor
	 *
	 * @param   array $attributes The data to populate the element with
	 */
	public function __construct($attributes)
	{
		foreach ($attributes as $name => $value)
		{
			$method = 'set' . ucfirst($name);

			if (is_callable([$this, $method]))
			{
				$this->$method($value);
			}
			else
			{
				$this->$name = $value;
			}
		}
	}

	/**
	 * Returns an array of variables assigned to this element.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return get_object_vars($this);
	}

	/**
	 * Gets the column name for an identifier
	 *
	 * @param   string  $identifier  The identifier
	 *
	 * @return string
	 */
	public function columnName($identifier)
	{
		return Normalise::toUnderscoreSeparated($identifier);
	}

	/**
	 * Gets the property name for an identifier
	 *
	 * @param   string $identifier The identifier
	 *
	 * @return  string
	 */
	public function propertyName($identifier)
	{
		return Normalise::toVariable($identifier);
	}

	/**
	 * Determine the basename of an id field
	 *
	 * @param   string $name The field name
	 *
	 * @return  string  The name without 'id' suffix
	 */
	protected function getBasename($name)
	{
		return preg_replace('~^(.*?)_?id$~i', '\1', $name);
	}
}
