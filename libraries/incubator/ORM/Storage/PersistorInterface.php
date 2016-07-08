<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage;

/**
 * Interface PersistorInterface
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
interface PersistorInterface
{
	/**
	 * Insert an entity.
	 *
	 * @param   object $entity The entity to store
	 *
	 * @return  void
	 */
	public function insert($entity);

	/**
	 * Update an entity.
	 *
	 * @param   object $entity The entity to store
	 *
	 * @return  void
	 */
	public function update($entity);

	/**
	 * Delete an entity.
	 *
	 * @param   object $entity The entity to sanitise
	 *
	 * @return  void
	 */
	public function delete($entity);
}
