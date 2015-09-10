<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

/**
 * Interface for cacheable events.
 * An event implementing this interface can be cached by a dispatcher implementing CachingDispatcherInterface
 *
 * @since  1.0
 */
interface CacheableEvent extends EventInterface
{
	/**
	 * Returns a unique identifier for caching, e.g. based on the currently set arguments
	 *
	 * @return  string
	 */
	public function getCacheIdentifier();
}
