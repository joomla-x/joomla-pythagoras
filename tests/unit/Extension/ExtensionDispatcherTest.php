<?php

namespace Joomla\Tests\Unit\Extension;

use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Extension\ExtensionDispatcher;
use Joomla\Extension\ExtensionFactoryInterface;
use Joomla\Extension\ExtensionInterface;

class ExtensionDispatcherTest extends \PHPUnit_Framework_TestCase
{

	public function testExtensionDispatcher()
	{
		/** @var ExtensionFactoryInterface|\PHPUnit_Framework_MockObject_MockObject $factory */
		$factory    = $this->getMockBuilder(ExtensionFactoryInterface::class)->getMock();
		$dispatcher = new ExtensionDispatcher($factory);

		$this->assertInstanceOf(DispatcherInterface::class, $dispatcher);
		$this->assertEmpty($dispatcher->getListeners('unit'));
	}

	public function testExtensionDispatcherListenersLoadedOnDispatch()
	{
		$testCallable = function ()
		{
		};

		/** @var  $plugin */
		$plugin = $this->getMockBuilder(ExtensionInterface::class)->getMock();
		$plugin->method('getListeners')->willReturn([
			$testCallable
		]);

		/** @var ExtensionFactoryInterface|\PHPUnit_Framework_MockObject_MockObject $factory */
		$factory = $this->getMockBuilder(ExtensionFactoryInterface::class)->getMock();
		$factory->expects($this->once())
				->method('getExtensions')
				->willReturn([
					$plugin
				]);
		$dispatcher = new ExtensionDispatcher($factory);
		$dispatcher->dispatch(new Event('unit'));

		$listeners = $dispatcher->getListeners('unit');
		$this->assertCount(1, $listeners);
		$this->assertEquals($testCallable, $listeners[0]);
	}
}
