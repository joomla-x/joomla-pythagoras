<?php
/**
 * Part of the Joomla Workflow Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Workflow\Entity;

/**
 * Class State
 *
 * @package  Joomla\Extension\Workflow
 *
 * @since    __DEPLOY_VERSION__
 */
class StateEntity
{
	/** @var  integer  The ID */
	public $id;

	/** @var  integer  The reference to the entity */
	public $entity_id;

	/** @var  integer  The reference to the state */
	public $state_id;
}
