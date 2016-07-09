<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage;

use Joomla\ORM\IdAccessorRegistry;

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
	 * @param   object             $entity The entity to store
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 */
	public function insert($entity, IdAccessorRegistry $idAccessorRegistry);

	/**
	 * Update an entity.
	 *
	 * @param   object             $entity The entity to insert
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 */
	public function update($entity, IdAccessorRegistry $idAccessorRegistry);

	/**
	 * Delete an entity.
	 *
	 * @param   object             $entity The entity to sanitise
	 * @param   IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return  void
	 */
	public function delete($entity, IdAccessorRegistry $idAccessorRegistry);
}
