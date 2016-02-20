<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Persistor;

use Joomla\ORM\Entity\EntityInterface;

/**
 * Interface PersistorInterface
 *
 * @package  Joomla/orm
 * @since    1.0
 */
interface PersistorInterface
{
	/**
	 * Store an entity.
	 *
	 * @param   EntityInterface $entity The entity to store
	 *
	 * @return  void
	 */
	public function store(EntityInterface $entity);

	/**
	 * Delete an entity.
	 *
	 * @param   EntityInterface $entity The entity to sanitise
	 *
	 * @return  void
	 */
	public function delete(EntityInterface $entity);
}
