<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Option
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class Option extends Element
{
	/**
	 * Constructor
	 *
	 * @param   array $attributes The data to populate the element with
	 */
	public function __construct($attributes)
	{
		$attributes['label'] = $attributes['#text'];
		unset($attributes['#text']);

		parent::__construct($attributes);
	}
}
