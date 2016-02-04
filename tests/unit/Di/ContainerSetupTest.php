<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Di;

use Joomla\Di\Container;

include_once 'Stubs/stubs.php';

/**
 * Tests for Container class.
 */
class ContainerSetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Callable object method.
     */
    public function callMe()
    {
        return 'called';
    }

    /**
     * @testdox Resources can be set up with Callables
     */
    public function testSetCallable()
    {
        $container = new Container();
        $container->set(
            'foo',
            array($this, 'callMe')
        );

        $this->assertEquals('called', $container->get('foo'));
    }

    /**
     * @testdox Resources can be set up with Closures
     */
    public function testSetClosure()
    {
        $container = new Container();
        $container->set(
            'foo',
            function () {
                return 'called';
            }
        );

        $this->assertEquals('called', $container->get('foo'));
    }

    /**
     * @testdox Resources can be scalar values
     */
    public function testSetNotCallable()
    {
        $container = new Container();
        $container->set('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @testdox Setting an existing protected resource throws an OutOfBoundsException
     * @expectedException  \OutOfBoundsException
     */
    public function testSetAlreadySetProtected()
    {
        $container = new Container();
        $container->set(
            'foo',
            function () {
            },
            false,
            true
        );
        $container->set(
            'foo',
            function () {
            },
            false,
            true
        );
    }

    /**
     * @testdox Setting an existing non-protected resource replaces the resource
     */
    public function testSetAlreadySetNotProtected()
    {
        $container = new Container();
        $container->set(
            'foo',
            function () {
                return 'original';
            }
        );
        $this->assertEquals('original', $container->get('foo'));

        $container->set(
            'foo',
            function () {
                return 'changed';
            }
        );
        $this->assertEquals('changed', $container->get('foo'));
    }

    /**
     * @testdox Default mode is 'not shared' and 'not protected'
     */
    public function testSetDefault()
    {
        $container = new Container();
        $container->set(
            'foo',
            function () {
                return new \StdClass;
            }
        );

        $this->assertFalse($container->isShared('foo'));
        $this->assertFalse($container->isProtected('foo'));
    }

    public function dataForSetFlags()
    {
        return array(
            'shared, protected' => array(
                'shared' => true,
                'protected' => true
            ),
            'shared, not protected' => array(
                'shared' => true,
                'protected' => false
            ),
            'not shared, protected' => array(
                'shared' => false,
                'protected' => true
            ),
            'not shared, not protected' => array(
                'shared' => false,
                'protected' => false
            ),
        );
    }

    /**
     * @dataProvider dataForSetFlags
     * @testdox      'shared' and 'protected' mode can be set independently
     */
    public function testSetSharedProtected($shared, $protected)
    {
        $container = new Container();
        $container->set(
            'foo',
            function () {
                return new \StdClass;
            },
            $shared,
            $protected
        );

        $this->assertEquals($shared, $container->isShared('foo'));
        $this->assertEquals($protected, $container->isProtected('foo'));
    }

    /**
     * @testdox The convenience method protect() sets resources as protected, but not as shared by default
     */
    public function testProtect()
    {
        $container = new Container();
        $container->protect(
            'foo',
            function () {
                return new \StdClass;
            }
        );

        $this->assertFalse($container->isShared('foo'));
        $this->assertTrue($container->isProtected('foo'));
    }

    /**
     * @testdox The convenience method protect() sets resources as shared when passed true as third arg
     */
    public function testProtectShared()
    {
        $container = new Container();
        $container->protect(
            'foo',
            function () {
                return new \StdClass;
            },
            true
        );

        $this->assertTrue($container->isShared('foo'));
        $this->assertTrue($container->isProtected('foo'));
    }

    /**
     * @testdox The convenience method share() sets resources as shared, but not as protected by default
     */
    public function testShare()
    {
        $container = new Container();
        $container->share(
            'foo',
            function () {
                return new \StdClass;
            }
        );

        $this->assertTrue($container->isShared('foo'));
        $this->assertFalse($container->isProtected('foo'));
    }

    /**
     * @testdox The convenience method share() sets resources as protected when passed true as third arg
     */
    public function testShareProtected()
    {
        $container = new Container();
        $container->share(
            'foo',
            function () {
                return new \StdClass;
            },
            true
        );

        $this->assertTrue($container->isShared('foo'));
        $this->assertTrue($container->isProtected('foo'));
    }

    /**
     * @testdox The callback gets the container instance as a parameter
     */
    public function testGetPassesContainerInstanceShared()
    {
        $container = new Container();
        $container->set(
            'foo',
            function ($c) {
                return $c;
            }
        );

        $this->assertSame($container, $container->get('foo'));
    }

}
