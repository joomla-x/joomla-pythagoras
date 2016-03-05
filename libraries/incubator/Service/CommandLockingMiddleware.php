<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use League\Tactician\Plugins\LockingMiddleware;

/**
 * Conditional locking middleware.
 *
 * Provides locking for Commands only; Queries are ignored.
 *
 * @package  Joomla/Service
 *
 * @since  __DEPLOY_VERSION__
 */
class CommandLockingMiddleware extends LockingMiddleware
{
	/**
	 * Execute the given command.
	 *
	 * @param   object    $message  The Command or Query to execute.
	 * @param   callable  $next     The next middleware object to be called.
	 *
	 * @return  mixed|void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function execute($message, callable $next)
	{
		// Only lock for Commands.
		if ($message instanceof Command)
		{
			return parent::execute($message, $next);
		}

		return $next($message);
	}
}
