<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

/**
 * Command Bus interface.
 *
 * @since  __DEPLOY__
 */
interface CommandBus
{
	/**
	 * Handle a command.
	 *
	 * @param   Command $command A command object.
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY__
	 */
	public function handle(Command $command);
}
