<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeInterface;

/**
 * Abstract ContentType
 *
 * @package  Joomla/Content
 * @since    1.0
 */
abstract class AbstractContentType implements ContentTypeInterface
{
	/**
	 * Magic getter.
	 *
	 * @param   string  $var  Name of the property
	 *
	 * @return  mixed
	 */
	public function __get($var)
	{
		if (isset($this->$var))
		{
			return $this->$var;
		}

		throw new \UnexpectedValueException("Unknown property $var");
	}
}
