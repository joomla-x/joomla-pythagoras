<?php
namespace Joomla\Tests\Unit\Plugin;

use Joomla\Plugin\Plugin;
use Joomla\Plugin\PluginInterface;
use Joomla\Tests\Unit\Plugin\Stubs\SimpleEventListener;

class PluginTest extends \PHPUnit_Framework_TestCase
{

	public function testPluginListeners ()
	{
		$plugin = new Plugin();

		$this->assertInstanceOf(PluginInterface::class, $plugin);
		$this->assertEmpty($plugin->getListeners('unit'));
	}

	public function testAddPluginListener ()
	{
		$plugin = new Plugin();
		$plugin->addListener('unit', new SimpleEventListener());

		$this->assertCount(1, $plugin->getListeners('unit'));
		$this->assertInstanceOf(SimpleEventListener::class, $plugin->getListeners('unit')[0]);
	}
}
