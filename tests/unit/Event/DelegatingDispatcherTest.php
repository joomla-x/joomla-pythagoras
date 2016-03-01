<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Event;

use Joomla\Event\DelegatingDispatcher;
use Joomla\Event\DispatcherInterface;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests for the DelegatingDispatcher class.
 *
 * @since  1.0
 */
class DelegatingDispatcherTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test the triggerEvent method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testTriggerEvent()
	{
		$event = 'onTest';

		/** @var DispatcherInterface|PHPUnit_Framework_MockObject_MockObject $mockedDispatcher */
		$mockedDispatcher = $this->getMock('Joomla\Event\DispatcherInterface');
		$mockedDispatcher->expects($this->once())
			->method('triggerEvent')
			->with($event);

		$delegating = new DelegatingDispatcher($mockedDispatcher);

		$delegating->triggerEvent($event);
	}
}