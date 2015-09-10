<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

/**
 * Interface for event dispatchers implementing event caching.
 *
 * @since  1.0
 */
interface CachingDispatcherInterface
{
	/**
	 * Clears the dispatcher's cache for a specific event name. If no event name is supplied the entire
	 * cache is cleared. If the event name supplied does not exist in the cache no operation is performed.
	 *
	 * @param   string|null  $name  The event name to uncache
	 *
	 * @return  void
	 */
	public function uncache($name = null);
}
