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
 * @since    __DEPLOY_VERSION__
 */
class EntityNotDefinedException extends OrmException
{
	/**
	 * EntityNotDefinedException constructor.
	 *
	 * @param   string           $entityClass  The entity class
	 * @param   int              $code         An error code, defaults to 0
	 * @param   \Exception|null  $previous     A previous exception, defaults to none
	 */
	public function __construct($entityClass, $code = 0, \Exception $previous = null)
	{
		parent::__construct("Entity '$entityClass' is not defined.", $code, $previous);
	}
}
