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
 *
 * @property string $text
 * @property integer $level
 */
abstract class abstractContentType implements ContentTypeInterface
{
	public function __get($var)
	{
		if (isset($this->$var))
		{
			return $this->$var;
		}

		throw new \UnexpectedValueException("Unknown property $var");
	}
}
