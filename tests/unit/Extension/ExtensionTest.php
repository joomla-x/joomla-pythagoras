<?php

namespace Joomla\Tests\Unit\Extension;

use Joomla\Extension\Extension;
use Joomla\Extension\ExtensionInterface;
use Joomla\Tests\Unit\Extension\Stubs\SimpleEventListener;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testExtensionListeners()
    {
        $plugin = new Extension();

        $this->assertInstanceOf(ExtensionInterface::class, $plugin);
        $this->assertEmpty($plugin->getListeners('unit'));
    }

    public function testAddExtensionListener()
    {
        $plugin = new Extension();
        $plugin->addListener('unit', new SimpleEventListener());

        $this->assertCount(1, $plugin->getListeners('unit'));
        $this->assertInstanceOf(SimpleEventListener::class, $plugin->getListeners('unit')[0]);
    }
}
