<?php

namespace Joomla\Tests\Unit\Extension;

use Joomla\Extension\FileExtensionFactory;
use Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener;
use League\Flysystem\Adapter\AbstractAdapter;

class FileExtensionFactoryTest extends \PHPUnit_Framework_TestCase
{

	public function testGetAllExtensions()
	{
		$pluginManifest = <<<EOL
listeners:
    UnitTestListener:
        class: \Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener
        events:
            onUnitTestEvent: onEventTest
EOL;

		$fs = $this->getMockBuilder(AbstractAdapter::class)->getMock();
		$fs->method('listContents')->willReturn([
			[
				'path' => 'plugin.yml'
			]
		]);
		$fs->method('read')->willReturn([
			'contents' => $pluginManifest
		]);

		$factory = new FileExtensionFactory($fs);
		$plugins = $factory->getExtensions();

		$this->assertCount(1, $plugins);

		$listeners = $plugins[0]->getListeners('onUnitTestEvent');
		$this->assertNotEmpty($listeners);
		$this->assertInstanceOf(SimpleEventListener::class, $listeners[0][0]);
		$this->assertEquals('onEventTest', $listeners[0][1]);
	}

	public function testGetTypeSpecificExtensions()
	{
		$pluginManifest  = <<<EOL
listeners:
    UnitTestListener:
        class: \Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener
        events:
            onUnitTestEvent: onEventTest
EOL;
		$pluginManifest1 = <<<EOL
listeners:
    UnitTestListener1:
        class: \Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener
        events:
            onUnitTestEvent1: onEventTest
EOL;

		$fs = $this->getMockBuilder(AbstractAdapter::class)->getMock();
		$fs->method('listContents')->willReturnOnConsecutiveCalls([
			[
				'path' => 'plugin.yml'
			]
		], [
			[
				'path' => 'plugin.yml'
			]
		]);
		$fs->method('read')->willReturnOnConsecutiveCalls([
			'contents' => $pluginManifest
		], [
			'contents' => $pluginManifest1
		]);
		$fs->method('applyPathPrefix')->willReturnOnConsecutiveCalls('test1', 'test2');

		$factory = new FileExtensionFactory($fs);
		$plugins = $factory->getExtensions('content');

		$listeners = $plugins[0]->getListeners('onUnitTestEvent');
		$this->assertNotEmpty($listeners);
		$this->assertInstanceOf(SimpleEventListener::class, $listeners[0][0]);
		$this->assertEquals('onEventTest', $listeners[0][1]);

		$pluginsAll = $factory->getExtensions();
		$this->assertNotEquals($plugins, $pluginsAll);

		/** @noinspection PhpUnusedLocalVariableInspection */
		$listeners = $pluginsAll[0]->getListeners('onUnitTestEvent');
		$this->assertEmpty($pluginsAll[0]->getListeners('onUnitTestEvent'));
		$this->assertNotEmpty($pluginsAll[0]->getListeners('onUnitTestEvent1'));
	}
}
