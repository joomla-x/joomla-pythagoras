<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Validator;

use Joomla\ORM\Entity\EntityInterface;

/**
 * Interface ValidatorInterface
 *
 * @package  Joomla/orm
 * @since    1.0
 */
interface ValidatorInterface
{
	/**
	 * Validate an entity.
	 *
	 * @param   EntityInterface $entity The entity to validate
	 *
	 * @return  boolean
	 */
	public function check(EntityInterface $entity);

	/**
	 * Sanitise an entity.
	 *
	 * @param   EntityInterface $entity The entity to sanitise
	 *
	 * @return  boolean
	 */
	public function sanitise(EntityInterface $entity);
}
