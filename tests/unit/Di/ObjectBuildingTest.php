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
class ObjectBuildingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox Building an object returns an instance of the requested class
     */
    public function testBuildObjectNoDependencies()
    {
        $container = new Container();
        $object = $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub1');

        $this->assertInstanceOf('Joomla\\Tests\\Unit\\Di\\Stub1', $object);
    }

    /**
     * @testdox Building a non-shared object returns a new object whenever requested
     */
    public function testBuildObject()
    {
        $container = new Container();
        $object = $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub1');

        $this->assertNotSame($object, $container->get('Joomla\\Tests\\Unit\\Di\\Stub1'));
        $this->assertNotSame($container->get('Joomla\\Tests\\Unit\\Di\\Stub1'), $container->get('Joomla\\Tests\\Unit\\Di\\Stub1'));
    }

    /**
     * @testdox Building a shared object returns the same object whenever requested
     */
    public function testBuildSharedObject()
    {
        $container = new Container();
        $object = $container->buildSharedObject('Joomla\\Tests\\Unit\\Di\\Stub1');

        $this->assertSame($object, $container->get('Joomla\\Tests\\Unit\\Di\\Stub1'));
        $this->assertSame($container->get('Joomla\\Tests\\Unit\\Di\\Stub1'), $container->get('Joomla\\Tests\\Unit\\Di\\Stub1'));
    }

    /**
     * @testdox Attempting to build a non-class returns false
     */
    public function testBuildObjectNonClass()
    {
        $container = new Container();
        $this->assertFalse($container->buildObject('foobar'));
    }

    /**
     * @testdox Dependencies are resolved from the container's known resources
     */
    public function testBuildObjectGetDependencyFromContainer()
    {
        $container = new Container();
        $container->set('Joomla\\Tests\\Unit\\Di\\StubInterface', function () {
            return new Stub1;
        });

        $object = $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub2');

        $this->assertInstanceOf('Joomla\\Tests\\Unit\\Di\\Stub1', $object->stub);
    }

    /**
     * @testdox Resources are created, if they are not present in the container
     */
    public function testGetMethodArgsConcreteClass()
    {
        $container = new Container();
        $object = $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub5');

        $this->assertInstanceOf('Joomla\\Tests\\Unit\\Di\\Stub4', $object->stub);
    }

    /**
     * @testdox Dependencies are resolved from their default values
     */
    public function testGetMethodArgsDefaultValues()
    {
        $container = new Container();
        $object = $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub6');

        $this->assertEquals('foo', $object->stub);
    }

    /**
     * @testdox A DependencyResolutionException is thrown, if an object can not be built due to unspecified constructor parameter types
     * @expectedException  \Joomla\Di\Exception\DependencyResolutionException
     */
    public function testGetMethodArgsCantResolve()
    {
        $container = new Container();
        $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub7');
    }

    /**
     * @testdox A DependencyResolutionException is thrown, if an object can not be built due to dependency on unknown interfaces
     * @expectedException  \Joomla\Di\Exception\DependencyResolutionException
     */
    public function testGetMethodArgsResolvedIsNotInstanceOfHintedDependency()
    {
        $container = new Container();
        $container->buildObject('Joomla\\Tests\\Unit\\Di\\Stub2');
    }

    /**
     * @testdox When a circular dependency is detected, a DependencyResolutionException is thrown (Bug #4)
     * @expectedException \Joomla\Di\Exception\DependencyResolutionException
     */
    public function testBug4()
    {
        $container = new Container();

        $fqcn = 'Extension\\vendor\\FooComponent\\FooComponent';
        $data = array();

        $container->set(
            $fqcn,
            function (Container $c) use ($fqcn, $data) {
                $instance = $c->buildObject($fqcn);
                $instance->setData($data);

                return $instance;
            }
        );

        $container->get($fqcn);
    }
}
