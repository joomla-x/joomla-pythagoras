<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Event;

use Joomla\Event\DelegatingDispatcher;
use Joomla\Event\Dispatcher;
use Joomla\Event\Event;

/**
 * Tests for the DelegatingDispatcher class.
 *
 * @since  1.0
 */
class DelegatingDispatcherTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The deprecated triggerEvent() method proxies the dispatch() method
	 */
	public function testTriggerEvent()
	{
		$event = 'onTest';

		/** @var Dispatcher|\PHPUnit_Framework_MockObject_MockObject $mockedDispatcher */
		$mockedDispatcher = $this->createMock(Dispatcher::class);
		$mockedDispatcher->expects($this->once())
		                 ->method('dispatch')
		                 ->with(new Event($event));

		$delegating = new DelegatingDispatcher($mockedDispatcher);

		/** @noinspection PhpDeprecationInspection */
		$delegating->triggerEvent($event);
	}
}
