<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Di;

use Joomla\Di\Container;

include_once 'Stubs/stubs.php';
include_once 'Stubs/ArbitraryInteropContainer.php';

/**
 * Tests for Container class.
 */
class HierachicalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox Child container has access to parent's resources
     */
    public function testCreateChild()
    {
        $container = new Container();
        $container->set('Joomla\\Tests\\Unit\\Di\\StubInterface', function () {
            return new Stub1;
        });

        $child = $container->createChild();
        $this->assertInstanceOf('Joomla\\Tests\\Unit\\Di\\Stub1', $child->get('Joomla\\Tests\\Unit\\Di\\StubInterface'));
    }

    /**
     * @testdox Child container resolves parent's alias to parent's resource
     */
    public function testChildResolveAlias()
    {
        $container = new Container();
        $container->set('Joomla\\Tests\\Unit\\Di\\StubInterface', function () {
            return new Stub1;
        });
        $container->alias('stub', 'Joomla\\Tests\\Unit\\Di\\StubInterface');

        $child = $container->createChild();
        $this->assertInstanceOf('Joomla\\Tests\\Unit\\Di\\Stub1', $child->get('stub'));
    }

    /**
     * @testdox Container can decorate an arbitrary Interop compatible container
     */
    public function testDecorateArbitraryInteropContainer()
    {
        $container = new Container(new \ArbitraryInteropContainer());

        $this->assertTrue($container->has('aic_foo'), "Container does not know 'aic_foo'");
        $this->assertEquals('aic_foo_content', $container->get('aic_foo'), "Container does not return the correct value for 'aic_foo'");
    }

    /**
     * @testdox Container can manage an alias for a resource from an arbitrary Interop compatible container
     */
    public function testDecorateArbitraryInteropContainerAlias()
    {
        $container = new Container(new \ArbitraryInteropContainer());
        $container->alias('foo', 'aic_foo');

        $this->assertTrue($container->has('foo'), "Container does not know alias 'foo'");
        $this->assertEquals('aic_foo_content', $container->get('foo'), "Container does not return the correct value for alias 'foo'");
    }

    /**
     * @testdox Resources from an arbitrary Interop compatible container are 'shared' and 'protected'
     */
    public function testDecorateArbitraryInteropContainerModes()
    {
        $container = new Container(new \ArbitraryInteropContainer());

        $this->assertTrue($container->isShared('aic_foo'), "'aic_foo' is expected to be shared");
        $this->assertTrue($container->isProtected('aic_foo'), "'aic_foo' is expected to be protected");
    }
}
