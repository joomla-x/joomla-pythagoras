<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM;

use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Id\IdAccessorRegistry;
use Joomla\Tests\Unit\ORM\Mocks\Foo;
use Joomla\Tests\Unit\ORM\Mocks\User;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Id accessor registry
 */
class IdAccessorRegistryTest extends TestCase
{
	/** @var IdAccessorRegistry The registry to use in tests */
	private $registry = null;

	/** @var User An entity to use in the tests */
	private $entity1 = null;

	/**
	 * Sets up the tests
	 */
	public function setUp()
	{
		$this->registry = new IdAccessorRegistry();
		$this->registry->registerIdAccessors(
			User::class,
			function (User $user)
			{
				return $user->getId();
			},
			function (User $user, $id)
			{
				$user->setId($id);
			}
		);
		$this->entity1 = new User(724, 'foo');
	}

	/**
	 * @testdox The accessors are automatically set, if the object has an 'id' property
	 */
	public function testIdPropertyAccessorsAutomaticallySet()
	{
		$entity = new Foo;;

		$this->assertEquals(-1, $this->registry->getEntityId($entity));

		$this->registry->setEntityId($entity, 2);
		$this->assertEquals(2, $this->registry->getEntityId($entity));
	}

	/**
	 * @testdox Getting an entity Id
	 */
	public function testGettingEntityId()
	{
		$this->assertEquals(724, $this->registry->getEntityId($this->entity1));
	}

	/**
	 * @testdox Getting an entity Id without registering a getter
	 */
	public function testGettingEntityIdWithoutRegisteringGetter()
	{
		$this->expectException(OrmException::class);
		$entity = $this->getMockBuilder(User::class)
		               ->disableOriginalConstructor()
		               ->setMockClassName("Foo")
		               ->getMock();
		$this->registry->getEntityId($entity);
	}

	/**
	 * @testdox Getting the Id with reflection for a non-existent property
	 */
	public function testGettingIdWithReflectionForNonExistentProperty()
	{
		$this->expectException(OrmException::class);
		$this->registry->registerReflectionIdAccessors(Foo::class, "doesNotExist");
		$foo = new Foo();
		$this->registry->getEntityId(new Foo());
	}

	/**
	 * Tests reflection accessors
	 */
	public function testReflectionAccessors()
	{
		$this->registry->registerReflectionIdAccessors(Foo::class, "id");
		$foo = new Foo();
		$this->registry->setEntityId($foo, 24);
		$this->assertEquals(24, $this->registry->getEntityId($foo));
	}

	/**
	 * Tests registering an array of class names
	 */
	public function testRegisteringArrayOfClassNames()
	{
		$entity1 = $this->getMockBuilder(User::class)
		                ->setMockClassName("FooEntity")
		                ->disableOriginalConstructor()
		                ->getMock();
		$entity1->expects($this->any())
		        ->method("setId")
		        ->with(123);
		$entity2 = $this->getMockBuilder(User::class)
		                ->setMockClassName("BarEntity")
		                ->disableOriginalConstructor()
		                ->getMock();
		$entity2->expects($this->any())
		        ->method("setId")
		        ->with(456);
		$getter = function ($entity)
		{
			return 123;
		};
		$setter = function ($entity, $id)
		{
			/** @var User $entity */
			$entity->setId($id);
		};
		$this->registry->registerIdAccessors(["FooEntity", "BarEntity"], $getter, $setter);
		$this->assertEquals(123, $this->registry->getEntityId($entity1));
		$this->registry->setEntityId($entity1, 123);
		$this->assertEquals(123, $this->registry->getEntityId($entity2));
		$this->registry->setEntityId($entity2, 456);
	}

	/**
	 * Tests setting an entity Id
	 */
	public function testSettingEntityId()
	{
		$this->registry->setEntityId($this->entity1, 333);
		$this->assertEquals(333, $this->entity1->getId());
	}

	/**
	 * Tests setting an entity Id without registering a setter
	 */
	public function testSettingEntityIdWithoutRegisteringGetter()
	{
		$this->expectException(OrmException::class);
		$entity = $this->getMockBuilder(User::class)
		               ->disableOriginalConstructor()
		               ->setMockClassName("Foo")
		               ->getMock();
		$this->registry->setEntityId($entity, 24);
	}

	/**
	 * Tests setting the Id with reflection for a non-existent property
	 */
	public function testSettingIdWithReflectionForNonExistentProperty()
	{
		$this->expectException(OrmException::class);
		$this->registry->registerReflectionIdAccessors(Foo::class, "doesNotExist");
		$this->registry->setEntityId(new Foo(), 24);
	}
}
