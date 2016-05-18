<?php

namespace Joomla\Tests\Unit\Extension;

use Joomla\Extension\FileExtensionFactory;
use Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener;
use League\Flysystem\Adapter\AbstractAdapter;

class FileExtensionFactoryTest extends \PHPUnit_Framework_TestCase
{

	public function testGetAllExtensions()
	{
		$extensionManifest = <<<EOL
listeners:
    UnitTestListener:
        class: \Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener
        events:
            onUnitTestEvent: onEventTest
EOL;

		$fs = $this->getMockBuilder(AbstractAdapter::class)->getMock();
		$fs->method('listContents')->willReturn([
			[
				'path' => 'extension.yml'
			]
		]);
		$fs->method('read')->willReturn([
			'contents' => $extensionManifest
		]);

		$factory = new FileExtensionFactory($fs);
		$extensions = $factory->getExtensions();

		$this->assertCount(1, $extensions);

		$listeners = $extensions[0]->getListeners('onUnitTestEvent');
		$this->assertNotEmpty($listeners);
		$this->assertInstanceOf(SimpleEventListener::class, $listeners[0][0]);
		$this->assertEquals('onEventTest', $listeners[0][1]);
	}

	public function testGetTypeSpecificExtensions()
	{
		$extensionManifest  = <<<EOL
listeners:
    UnitTestListener:
        class: \Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener
        events:
            onUnitTestEvent: onEventTest
EOL;
		$extensionManifest1 = <<<EOL
listeners:
    UnitTestListener1:
        class: \Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener
        events:
            onUnitTestEvent1: onEventTest
EOL;

		$fs = $this->getMockBuilder(AbstractAdapter::class)->getMock();
		$fs->method('listContents')->willReturnOnConsecutiveCalls([
			[
				'path' => 'extension.yml'
			]
		], [
			[
				'path' => 'extension.yml'
			]
		]);
		$fs->method('read')->willReturnOnConsecutiveCalls([
			'contents' => $extensionManifest
		], [
			'contents' => $extensionManifest1
		]);
		$fs->method('applyPathPrefix')->willReturnOnConsecutiveCalls('test1', 'test2');

		$factory = new FileExtensionFactory($fs);
		$extensions = $factory->getExtensions('content');

		$listeners = $extensions[0]->getListeners('onUnitTestEvent');
		$this->assertNotEmpty($listeners);
		$this->assertInstanceOf(SimpleEventListener::class, $listeners[0][0]);
		$this->assertEquals('onEventTest', $listeners[0][1]);

		$extensionsAll = $factory->getExtensions();
		$this->assertNotEquals($extensions, $extensionsAll);

		/** @noinspection PhpUnusedLocalVariableInspection */
		$listeners = $extensionsAll[0]->getListeners('onUnitTestEvent');
		$this->assertEmpty($extensionsAll[0]->getListeners('onUnitTestEvent'));
		$this->assertNotEmpty($extensionsAll[0]->getListeners('onUnitTestEvent1'));
	}
}
