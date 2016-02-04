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
 * Query Bus interface.
 * 
 * @since  __DEPLOY__
 */
interface QueryBus
{
	/**
	 * Handle a command.
	 * 
	 * @param   Query  $query  A query object.
	 * 
	 * @return  mixed
	 * 
	 * @since   __DEPLOY__
	 */
	public function handle(Query $query);
}
