<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Event;

use Joomla\Event\Dispatcher;
use Joomla\Event\Event;
use Joomla\Event\EventImmutable;
use Joomla\Event\EventInterface;
use Joomla\Event\Priority;
use Joomla\Tests\Unit\Event\Stubs\FirstListener;
use Joomla\Tests\Unit\Event\Stubs\SecondListener;
use Joomla\Tests\Unit\Event\Stubs\SomethingListener;
use Joomla\Tests\Unit\Event\Stubs\ThirdListener;

/**
 * Tests for the Dispatcher class.
 *
 * @since  1.0
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Object under tests.
	 *
	 * @var    Dispatcher
	 *
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->instance = new Dispatcher;
	}

	/**
	 * Test the setEvent method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetEvent()
	{
		$event = new Event('onTest');
		$this->instance->setEvent($event);
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($event, $this->instance->getEvent('onTest'));

		$immutableEvent = new EventImmutable('onAfterSomething');
		$this->instance->setEvent($immutableEvent);
		$this->assertTrue($this->instance->hasEvent('onAfterSomething'));
		$this->assertSame($immutableEvent, $this->instance->getEvent('onAfterSomething'));

		// Setting an existing event will replace the old one.
		$eventCopy = new Event('onTest');
		$this->instance->setEvent($eventCopy);
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($eventCopy, $this->instance->getEvent('onTest'));
	}

	/**
	 * Test the addEvent method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddEvent()
	{
		$event = new Event('onTest');
		$this->instance->addEvent($event);
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($event, $this->instance->getEvent('onTest'));

		$immutableEvent = new EventImmutable('onAfterSomething');
		$this->instance->addEvent($immutableEvent);
		$this->assertTrue($this->instance->hasEvent('onAfterSomething'));
		$this->assertSame($immutableEvent, $this->instance->getEvent('onAfterSomething'));

		// Adding an existing event will have no effect.
		$eventCopy = new Event('onTest');
		$this->instance->addEvent($eventCopy);
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($event, $this->instance->getEvent('onTest'));
	}

	/**
	 * Test the hasEvent method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testHasEvent()
	{
		$this->assertFalse($this->instance->hasEvent('onTest'));

		$event = new Event('onTest');
		$this->instance->addEvent($event);
		$this->assertTrue($this->instance->hasEvent($event));
	}

	/**
	 * Test the getEvent method when the event doesn't exist.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetEventNonExisting()
	{
		$this->assertNull($this->instance->getEvent('non-existing'));
		$this->assertFalse($this->instance->getEvent('non-existing', false));
	}

	/**
	 * Test the removeEvent method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testRemoveEvent()
	{
		// No exception.
		$this->instance->removeEvent('non-existing');

		$event = new Event('onTest');
		$this->instance->addEvent($event);

		// Remove by passing the instance.
		$this->instance->removeEvent($event);
		$this->assertFalse($this->instance->hasEvent('onTest'));

		$this->instance->addEvent($event);

		// Remove by name.
		$this->instance->removeEvent('onTest');
		$this->assertFalse($this->instance->hasEvent('onTest'));
	}

	/**
	 * Test the getEvents method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetEvents()
	{
		$this->assertEmpty($this->instance->getEvents());

		$event1 = new Event('onBeforeTest');
		$event2 = new Event('onTest');
		$event3 = new Event('onAfterTest');

		$this->instance->addEvent($event1)
		               ->addEvent($event2)
		               ->addEvent($event3);

		$expected = [
			'onBeforeTest' => $event1,
			'onTest'       => $event2,
			'onAfterTest'  => $event3
		];

		$this->assertSame($expected, $this->instance->getEvents());
	}

	/**
	 * Test the clearEvents method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testClearEvents()
	{
		$event1 = new Event('onBeforeTest');
		$event2 = new Event('onTest');
		$event3 = new Event('onAfterTest');

		$this->instance->addEvent($event1)
		               ->addEvent($event2)
		               ->addEvent($event3);

		$this->instance->clearEvents();

		$this->assertFalse($this->instance->hasEvent('onBeforeTest'));
		$this->assertFalse($this->instance->hasEvent('onTest'));
		$this->assertFalse($this->instance->hasEvent('onAfterTest'));
		$this->assertEmpty($this->instance->getEvents());
	}

	/**
	 * Test the countEvents method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCountEvents()
	{
		$this->assertEquals(0, $this->instance->countEvents());

		$event1 = new Event('onBeforeTest');
		$event2 = new Event('onTest');
		$event3 = new Event('onAfterTest');

		$this->instance->addEvent($event1)
		               ->addEvent($event2)
		               ->addEvent($event3);

		$this->assertEquals(3, $this->instance->countEvents());
	}

	/**
	 * Test the addListener method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddListener()
	{
		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener1, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener1, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener2, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener2, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener3, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$this->assertTrue($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener1, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener1, 'onAfterSomething']));

		$this->assertTrue($this->instance->hasListener([$listener2, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener2, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener2, 'onAfterSomething']));

		$this->assertTrue($this->instance->hasListener([$listener3, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onAfterSomething']));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [
			$listener1,
			'onBeforeSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [
			$listener1,
			'onSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', [
			$listener1,
			'onAfterSomething'
		]));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [
			$listener2,
			'onBeforeSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [
			$listener2,
			'onSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', [
			$listener2,
			'onAfterSomething'
		]));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [
			$listener3,
			'onBeforeSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [
			$listener3,
			'onSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', [
			$listener3,
			'onAfterSomething'
		]));
	}

	/**
	 * Test the addListener method by specifying the events and priorities.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddListenerSpecifiedPriorities()
	{
		$listener = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener, 'onBeforeSomething'], Priority::MIN)
		               ->addListener('onSomething', [$listener, 'onSomething'], Priority::ABOVE_NORMAL)
		               ->addListener('onAfterSomething', [$listener, 'onAfterSomething'], Priority::MAX);

		$this->assertTrue($this->instance->hasListener([$listener, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onAfterSomething']));

		$this->assertEquals(Priority::MIN, $this->instance->getListenerPriority('onBeforeSomething', [
			$listener,
			'onBeforeSomething'
		]));
		$this->assertEquals(Priority::ABOVE_NORMAL, $this->instance->getListenerPriority('onSomething', [
			$listener,
			'onSomething'
		]));
		$this->assertEquals(Priority::MAX, $this->instance->getListenerPriority('onAfterSomething', [
			$listener,
			'onAfterSomething'
		]));
	}

	/**
	 * Test the addListener method with a closure listener.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddClosureListener()
	{
		$listener = function (EventInterface $event)
		{
		};

		$this->instance->addListener('onSomething', $listener, Priority::HIGH)
		               ->addListener('onAfterSomething', $listener, Priority::NORMAL);

		$this->assertTrue($this->instance->hasListener($listener, 'onSomething'));
		$this->assertTrue($this->instance->hasListener($listener, 'onAfterSomething'));

		$this->assertEquals(Priority::HIGH, $this->instance->getListenerPriority('onSomething', $listener));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', $listener));
	}

	/**
	 * Test the getListenerPriority method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetListenerPriority()
	{
		$listener = new SomethingListener;
		$this->instance->addListener('onSomething', [$listener, 'onSomething']);

		$this->assertEquals(
			Priority::NORMAL,
			$this->instance->getListenerPriority(
				'onSomething',
				[$listener, 'onSomething']
			)
		);
	}

	/**
	 * Test the getListeners method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetListeners()
	{
		$this->assertEmpty($this->instance->getListeners('onSomething'));

		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener1, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener1, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener2, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener2, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener3, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$onBeforeSomethingListeners = $this->instance->getListeners('onBeforeSomething');

		$this->assertSame([$listener1, 'onBeforeSomething'], $onBeforeSomethingListeners[0]);
		$this->assertSame([$listener2, 'onBeforeSomething'], $onBeforeSomethingListeners[1]);
		$this->assertSame([$listener3, 'onBeforeSomething'], $onBeforeSomethingListeners[2]);

		$onSomethingListeners = $this->instance->getListeners('onSomething');

		$this->assertSame([$listener1, 'onSomething'], $onSomethingListeners[0]);
		$this->assertSame([$listener2, 'onSomething'], $onSomethingListeners[1]);
		$this->assertSame([$listener3, 'onSomething'], $onSomethingListeners[2]);

		$onAfterSomethingListeners = $this->instance->getListeners('onAfterSomething');

		$this->assertSame([$listener1, 'onAfterSomething'], $onAfterSomethingListeners[0]);
		$this->assertSame([$listener2, 'onAfterSomething'], $onAfterSomethingListeners[1]);
		$this->assertSame([$listener3, 'onAfterSomething'], $onAfterSomethingListeners[2]);
	}

	/**
	 * Test the hasListener method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testHasListener()
	{
		$listener = new SomethingListener;
		$this->instance->addListener('onSomething', [$listener, 'onSomething']);
		$this->assertTrue($this->instance->hasListener([$listener, 'onSomething'], 'onSomething'));
	}

	/**
	 * Test the removeListener method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testRemoveListeners()
	{
		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething'])
		               ->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething'])
		               ->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);

		// Remove the listener from a specific event.
		$this->instance->removeListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);

		$this->assertFalse($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener2, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onBeforeSomething']));
	}

	/**
	 * Test the clearListeners method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testClearListeners()
	{
		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener1, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener1, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener2, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener2, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener3, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		// Test without specified event.
		$this->instance->clearListeners();

		$this->assertFalse($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertFalse($this->instance->hasListener([$listener2, 'onSomething']));
		$this->assertFalse($this->instance->hasListener([$listener3, 'onAfterSomething']));

		// Test with an event specified.

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener1, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener1, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener2, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener2, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener3, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$this->instance->clearListeners('onSomething');

		$this->assertTrue($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertFalse($this->instance->hasListener([$listener2, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onAfterSomething']));

		$this->assertFalse($this->instance->hasListener([$listener1, 'onSomething']));
		$this->assertFalse($this->instance->hasListener([$listener3, 'onSomething']));
	}

	/**
	 * Test the clearListeners method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCountListeners()
	{
		$this->assertEquals(0, $this->instance->countListeners('onTest'));

		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener1, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener1, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener2, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener2, 'onAfterSomething'])
		               ->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething'])
		               ->addListener('onSomething', [$listener3, 'onSomething'])
		               ->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$this->assertEquals(3, $this->instance->countListeners('onSomething'));
	}

	/**
	 * Test the addSubscriber method.
	 *
	 * @return  void
	 */
	public function testAddSubscriber()
	{
		$listener = new SomethingListener;

		// Add our event subscriber
		$this->instance->addSubscriber($listener);

		$this->assertTrue($this->instance->hasListener([$listener, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onAfterSomething']));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [
			$listener,
			'onBeforeSomething'
		]));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [
			$listener,
			'onSomething'
		]));
		$this->assertEquals(Priority::HIGH, $this->instance->getListenerPriority('onAfterSomething', [
			$listener,
			'onAfterSomething'
		]));
	}

	/**
	 * Test the removeSubscriber method.
	 *
	 * @return  void
	 */
	public function testRemoveSubscriber()
	{
		$listener = new SomethingListener;

		// Add our event subscriber
		$this->instance->addSubscriber($listener);

		// And now remove it
		$this->instance->removeSubscriber($listener);

		$this->assertFalse($this->instance->hasListener([$listener, 'onBeforeSomething']));
		$this->assertFalse($this->instance->hasListener([$listener, 'onSomething']));
		$this->assertFalse($this->instance->hasListener([$listener, 'onAfterSomething']));
	}
}
