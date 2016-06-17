<?php
namespace Joomla\Tests\Unit\Cms\ServiceProvider;

use Joomla\Cms\ServiceProvider\SessionServiceProvider;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionServiceProviderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox The SessionServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestSessionServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new SessionServiceProvider());
	}

	/**
	 * @testdox The SessionServiceProvider adds a SessionInterface to a
	 * container
	 */
	public function testSessionServiceProviderCreatesSessionInterface()
	{
		$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
		$request->method('getCookieParams')->willReturn([
				'unit' => 'test'
		]);

		$container = new Container();
		$container->set('Request', $request);

		$service = new SessionServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(SessionInterface::class, $container->get('Session'));
	}

	/**
	 * @testdox The SessionServiceProvider adds an SessionInterface to a
	 * container with an alias
	 */
	public function testSessionServiceProviderCreatesDispatcherWithAlias()
	{
		$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
		$request->method('getCookieParams')->willReturn([
				'unit' => 'test'
		]);

		$container = new Container();
		$container->set('Request', $request);

		$service = new SessionServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(SessionInterface::class, $container->get('unit'));
	}
}
