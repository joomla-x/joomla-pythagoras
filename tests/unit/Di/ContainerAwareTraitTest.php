<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Di;

use Joomla\Di\Container;

/**
 * Tests for ContainerAwareTrait class.
 */
class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @var    \Joomla\Di\ContainerAwareTrait */
    protected $object;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        // Only run tests on PHP 5.4+
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            static::markTestSkipped('Traits are not available in PHP < 5.4');
        }
    }

    /**
     * @testdox Container can be set with setContainer() and retrieved with getContainer()
     */
    public function testGetContainer()
    {
        $container = new Container();
        $trait = $this->getObjectForTrait('\\Joomla\\Di\\ContainerAwareTrait');
        $trait->setContainer($container);

        $this->assertSame($container, $trait->getContainer());
    }

    /**
     * @testdox getContainer() throws an ContainerNotFoundException, if no container is set
     * @expectedException   \Joomla\Di\Exception\ContainerNotFoundException
     */
    public function testGetContainerException()
    {
        $trait = $this->getObjectForTrait('\\Joomla\\Di\\ContainerAwareTrait');
        $trait->getContainer();
    }
}
