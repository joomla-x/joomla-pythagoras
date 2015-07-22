<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

/**
 * Defines the trait for a Cacheable Event.
 *
 * @since  __DEPLOY_VERSION__
 */
trait CacheableEventTrait
{
	/**
	 * The event cache identifier. Depends only on the properties of the object at the time of the first call of
	 * getCacheIdentifier.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $cacheIdentifier = null;

	/**
	 * Returns a unique identified for caching, based on the currently set arguments.
	 *
	 * @return  string
	 */
	public function getCacheIdentifier()
	{
		/**
		 * Initialise the cacheIdentifier if necessary. This is implemented as a private property instead of being
		 * dynamically computed for two reasons:
		 * 1. Performance
		 * 2. Once a mutable Event is dispatched its arguments may be modified. Reusing the same Event object would lead
		 *    to the cache being ignored.
		 */
		if (is_null($this->cacheIdentifier))
		{
			$this->cacheIdentifier = sha1(serialize($this->arguments));
		}

		return $this->cacheIdentifier;
	}
}
