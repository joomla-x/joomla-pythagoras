<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Collection;

/**
 * Interface CollectionInterface
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
interface CollectionInterface
{
	/**
	 * Get the status of the collection.
	 *
	 * @return  int  The status, one of the \Joomla\ORM\Status constants
	 *
	 * @see     \Joomla\ORM\Status
	 */
	public function status();

	/**
	 * Get the ids of the collection items
	 *
	 * @return  array  List of ids
	 */
	public function getIds();
}
