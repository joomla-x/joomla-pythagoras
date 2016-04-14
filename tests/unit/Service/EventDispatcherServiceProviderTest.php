<?php
namespace Joomla\Tests\Unit\Service;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Service\EventDispatcherServiceProvider;
use League\Flysystem\AdapterInterface;
use Joomla\Event\Dispatcher;
use Joomla\Tests\Unit\Service\Stubs\SimpleEventListener;

class EventDispatcherServiceProviderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox The EventDispatcherServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestEventDispatcherServiceProviderImplementsTheServiceProviderInterface ()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new EventDispatcherServiceProvider());
	}

	/**
	 * @testdox The EventDispatcherServiceProvider adds an EventDispatcher to a
	 * container
	 */
	public function testEventDispatcherServiceProviderCreatesDispatcher ()
	{
		$container = new Container();

		$service = new EventDispatcherServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(DispatcherInterface::class, $container->get('EventDispatcher'));
	}

	/**
	 * @testdox The EventDispatcherServiceProvider adds an EventDispatcher to a
	 * container and reads the listeners
	 */
	public function testEventDispatcherServiceProviderCreatesDispatcherWithListeners ()
	{
		$pluginManifest = <<<EOL
listeners:
    UpperCaseListener:
        class: \Joomla\Tests\Unit\Service\Stubs\SimpleEventListener
        events:
            onUnitTestEvent: onEventTest
EOL;
		$fs = $this->getMockBuilder(AdapterInterface::class)->getMock();
		$fs->method('listContents')->willReturn([
				[
						'path' => 'plugin.yml'
				]
		]);
		$fs->method('read')->willReturn(['contents'=>$pluginManifest]);
		$container = new Container();
		$container->set('JPATH_ROOT', $fs);

		$service = new EventDispatcherServiceProvider();
		$service->register($container);

		/** @var Dispatcher $dispatcher **/
		$dispatcher = $container->get('EventDispatcher');
		$this->assertInstanceOf(Dispatcher::class, $dispatcher);

		$listeners = $dispatcher->getListeners('onUnitTestEvent');
		$this->assertNotEmpty($listeners);
		$this->assertInstanceOf(SimpleEventListener::class, $listeners[0][0]);
		$this->assertEquals('onEventTest', $listeners[0][1]);
	}
}
