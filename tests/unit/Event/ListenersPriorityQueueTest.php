<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Event;

use Joomla\Event\ListenersPriorityQueue;

/**
 * Tests for the ListenersPriorityQueue class.
 *
 * @since  1.0
 */
class ListenersPriorityQueueTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Object under tests.
	 *
	 * @var    ListenersPriorityQueue
	 *
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Test the add method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAdd()
	{
		$listener = function ()
		{
		};

		$this->instance->add($listener, 0);

		$this->assertTrue($this->instance->has($listener));
		$this->assertEquals(0, $this->instance->getPriority($listener));
	}

	/**
	 * Test adding an existing listener will have no effect.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddExisting()
	{
		$listener = function ()
		{
		};

		$this->instance->add($listener, 5);
		$this->instance->add($listener, 0);

		$this->assertTrue($this->instance->has($listener));
		$this->assertEquals(5, $this->instance->getPriority($listener));
	}

	/**
	 * Test the getPriority method when the listener wasn't added.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPriorityNonExisting()
	{
		$listener = function ()
		{
		};

		$this->assertNull($this->instance->getPriority($listener));

		$this->assertFalse(
			$this->instance->getPriority(
				function ()
				{
				},
				false
			)
		);
	}

	/**
	 * Test the remove method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testRemove()
	{
		$listener1 = function ()
		{
		};

		$listener2 = function ()
		{
			return false;
		};

		$this->instance->add($listener1, 0);

		// Removing a non existing listener has no effect.
		$this->instance->remove($listener2);

		$this->assertTrue($this->instance->has($listener1));

		$this->instance->add($listener2, 0);

		$this->assertTrue($this->instance->has($listener1));
		$this->assertTrue($this->instance->has($listener2));

		$this->instance->remove($listener1);

		$this->assertFalse($this->instance->has($listener1));
		$this->assertTrue($this->instance->has($listener2));

		$this->instance->remove($listener2);

		$this->assertFalse($this->instance->has($listener1));
		$this->assertFalse($this->instance->has($listener2));
	}

	/**
	 * Test the getAll method.
	 * All listeners with the same priority must be sorted in the order
	 * they were added.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetAll()
	{
		$this->assertEmpty($this->instance->getAll());

		$listener0 = function ()
		{
		};

		$listener1 = function ()
		{
			return false;
		};

		$this->instance->add($listener0, 10);
		$this->instance->add($listener1, 3);

		$listeners = $this->instance->getAll();

		$this->assertSame($listeners[0], $listener0);
		$this->assertSame($listeners[1], $listener1);
	}

	/**
	 * Test the getIterator method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetIterator()
	{
		$listener0 = function ()
		{
		};

		$listener1 = function ()
		{
			return false;
		};

		$this->instance->add($listener0, 10);
		$this->instance->add($listener1, 3);

		$listeners = array();

		foreach ($this->instance as $listener)
		{
			$listeners[] = $listener;
		}

		$this->assertSame($listeners[0], $listener0);
		$this->assertSame($listeners[1], $listener1);
	}

	/**
	 * Test that ListenersPriorityQueue is not a heap.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetIteratorMultipleIterations()
	{
		$listener0 = function ()
		{
		};

		$listener1 = function ()
		{
			return false;
		};

		$this->instance->add($listener0, 0);
		$this->instance->add($listener1, 1);

		$firstListeners = array();

		foreach ($this->instance as $listener)
		{
			$firstListeners[] = $listener;
		}

		$secondListeners = array();

		foreach ($this->instance as $listener)
		{
			$secondListeners[] = $listener;
		}

		$this->assertSame($firstListeners, $secondListeners);
	}

	/**
	 * Test the count method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCount()
	{
		$this->assertCount(0, $this->instance);

		$listener1 = function ()
		{
		};

		$listener2 = function ()
		{
			return false;
		};

		$this->instance->add($listener1, 0);
		$this->instance->add($listener2, 0);

		$this->assertCount(2, $this->instance);
	}

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
		$this->instance = new ListenersPriorityQueue;
	}
}
