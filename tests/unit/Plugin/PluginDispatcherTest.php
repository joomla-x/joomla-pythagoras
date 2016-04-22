<?php

namespace Joomla\Tests\Unit\Plugin;

use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Plugin\PluginDispatcher;
use Joomla\Plugin\PluginFactoryInterface;
use Joomla\Plugin\PluginInterface;

class PluginDispatcherTest extends \PHPUnit_Framework_TestCase
{

	public function testPluginDispatcher()
	{
		/** @var PluginFactoryInterface|\PHPUnit_Framework_MockObject_MockObject $factory */
		$factory    = $this->getMockBuilder(PluginFactoryInterface::class)->getMock();
		$dispatcher = new PluginDispatcher($factory);

		$this->assertInstanceOf(DispatcherInterface::class, $dispatcher);
		$this->assertEmpty($dispatcher->getListeners('unit'));
	}

	public function testPluginDispatcherListenersLoadedOnDispatch()
	{
		$testCallable = function ()
		{
		};

		/** @var  $plugin */
		$plugin = $this->getMockBuilder(PluginInterface::class)->getMock();
		$plugin->method('getListeners')->willReturn([
			$testCallable
		]);

		/** @var PluginFactoryInterface|\PHPUnit_Framework_MockObject_MockObject $factory */
		$factory = $this->getMockBuilder(PluginFactoryInterface::class)->getMock();
		$factory->expects($this->once())
				->method('getPlugins')
				->willReturn([
					$plugin
				]);
		$dispatcher = new PluginDispatcher($factory);
		$dispatcher->dispatch(new Event('unit'));

		$listeners = $dispatcher->getListeners('unit');
		$this->assertCount(1, $listeners);
		$this->assertEquals($testCallable, $listeners[0]);
	}
}
