<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Event\Stubs;

use Joomla\Event\Event;
use Joomla\Event\Priority;
use Joomla\Event\SubscriberInterface;

/**
 * A listener listening to some events.
 *
 * @since  1.0
 */
class SomethingListener implements SubscriberInterface
{
	/**
	 * Listen to onBeforeSomething.
	 *
	 * @param   Event $event The event.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onBeforeSomething(Event $event)
	{
	}

	/**
	 * Listen to onSomething.
	 *
	 * @param   Event $event The event.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onSomething(Event $event)
	{
	}

	/**
	 * Listen to onAfterSomething.
	 *
	 * @param   Event $event The event.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterSomething(Event $event)
	{
	}

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * The array keys are event names and the value can be:
	 *
	 *  - The method name to call (priority defaults to 0)
	 *  - An array composed of the method name to call and the priority
	 *
	 * For instance:
	 *
	 *  * array('eventName' => 'methodName')
	 *  * array('eventName' => array('methodName', $priority))
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getSubscribedEvents()
	{
		return [
			'onBeforeSomething' => 'onBeforeSomething',
			'onSomething'       => 'onSomething',
			'onAfterSomething'  => ['onAfterSomething', Priority::HIGH]
		];
	}
}
