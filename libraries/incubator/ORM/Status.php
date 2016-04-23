<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM;

/**
 * Class Status
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
abstract class Status
{
	/** Denotes, that the object represents the persisted data */
	const CLEAN = 0;

	/** Denotes, that the object is not yet persisted */
	const CREATED = 1;

	/** Denotes, that the object differs from the persisted data */
	const CHANGED = 2;

	/** Denotes, that the object not longer is persisted */
	const ERASED = 3;
}
