<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Exception;

/**
 * Class EntityNotDefinedException
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class EntityNotDefinedException extends OrmException
{
	public function __construct($entityClass, $code = 0, \Exception $previous = null)
	{
		parent::__construct("Entity '$entityClass' is not defined.", $code, $previous);
	}
}
