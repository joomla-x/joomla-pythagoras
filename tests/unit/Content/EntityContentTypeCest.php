<?php
/**
 * Part of the Joomla Framework Renderer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Content;

use Joomla\Content\Type\Article;
use Joomla\Content\Type\Teaser;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\Service\CommandBus;
use Joomla\Service\CommandBusBuilder;
use Joomla\Tests\Unit\Service\Stubs\MockEventDispatcher;
use UnitTester;

/**
 * Class EntityContentTypeCest
 *
 * @package Joomla\Tests\Unit\Content
 */
class EntityContentTypeCest
{
	/** @var  EntityInterface */
	private $entity;

	/** @var  CommandBus */
	private $commandBus;

	/** @var  Mock\Renderer */
	private $renderer;

	/**
	 * @param UnitTester $I
	 */
	public function _before(UnitTester $I)
	{
		$this->commandBus = (new CommandBusBuilder(new MockEventDispatcher))->getCommandBus();
		$this->renderer   = new Mock\Renderer;

		$builder = new EntityBuilder(new Locator([new RecursiveDirectoryStrategy(__DIR__ . '/data')]));
		$this->entity         = $builder->create('article');
		$this->entity->title  = 'Title';
		$this->entity->author = "Unknown";
		$this->entity->teaser = 'Teaser';
		$this->entity->body   = 'Body';
	}

	/**
	 * @testdox An entity based content element gets extended by horizontal components 
	 */
	public function testElementExtension(UnitTester $I)
	{
		$content = new Article($this->entity, $this->commandBus);

		$content->accept($this->renderer);

		$I->assertEquals(
			[
				[
					"article" => [
						[
							"headline" => [
								"text"  => "Title",
								"level" => 1
							]
						],
						[
							"attribution" => [
								"label" => "Written by",
								"name"  => "Unknown"
							]
						],
						[
							"paragraph" => [
								"text"    => "Teaser",
								"variant" => 1
							]
						],
						[
							"paragraph" => [
								"text"    => "Body",
								"variant" => 0
							]
						],
						[
							"attribution" => [
								"label" => "Extended",
								"name"  => "YES"
							]
						]
					]
				]
			],
			$this->renderer->data
		);
	}

	/**
	 * @testdox Properties can be mapped
	 */
	public function testPropertyMapping(UnitTester $I)
	{
		$content = new Teaser($this->entity, $this->commandBus, ['teaser' => 'body']);

		$content->accept($this->renderer);

		$I->assertEquals(
			[
				[
					"article" => [
						[
							"headline" => [
								"text"  => "Title",
								"level" => 1
							]
						],
						[
							"attribution" => [
								"label" => "Written by",
								"name"  => "Unknown"
							]
						],
						[
							"paragraph" => [
								"text"    => "Body",
								"variant" => 1
							]
						],
						[
							"attribution" => [
								"label" => "Extended",
								"name"  => "YES"
							]
						]
					]
				]
			],
			$this->renderer->data
		);
	}

	/**
	 * @testdox Accessing undefined properties leads to an exception
	 */
	public function testUndefinedProperty(UnitTester $I)
	{
		$content = new Teaser($this->entity, $this->commandBus);

		try {
			$var = $content->undefinedProperty;

			$I->fail('Expected an UnexpectedValueException, but no exception was thrown.');
		}
		catch (\UnexpectedValueException $e)
		{
			$I->assertContains('undefinedProperty', $e->getMessage());
		}
		catch (\Exception $e)
		{
			$I->fail('Expected an UnexpectedValueException, but got ' . get_class($e));
		}
	}
}
