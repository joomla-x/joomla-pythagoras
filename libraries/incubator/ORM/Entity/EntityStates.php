<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

/**
 * Defines different states of entities
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class EntityStates
{
	/** A new entity that will be registered */
	const QUEUED = 1;

	/** A registered, persisted entity */
	const REGISTERED = 2;

	/** An entity that is no longer registered */
	const UNREGISTERED = 3;

	/** An entity that will be unregistered */
	const DEQUEUED = 4;

	/** An entity that was never registered */
	const NEVER_REGISTERED = 5;
}
