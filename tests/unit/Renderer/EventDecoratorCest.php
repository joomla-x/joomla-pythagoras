<?php
/**
 * Part of the Joomla Framework Renderer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\Event\Dispatcher;
use Joomla\Renderer\Event\RenderContentTypeEvent;
use Joomla\Renderer\Event\RenderContentTypeSuccessEvent;
use Joomla\Renderer\EventDecorator;
use Joomla\Renderer\Renderer;
use Joomla\Renderer\RendererInterface;
use Mockery;
use UnitTester;

class EventDecoratorCest
{
	public function _before(UnitTester $I)
	{
	}

	public function _after(UnitTester $I)
	{
	}

	public function DecoratorDelegatesAllNonVisitMethods(UnitTester $I)
	{
		$mockMethods = [
			'registerContentType' => [
				'custom',
				function ()
				{
				},
				null
			],
			'close'               => [null],
			'detach'              => [null],
			'rewind'              => [null],
			'seek'                => [0, null],
			'getMetadata'         => [null],
			'__toString'          => ['content'],
			'read'                => [8192, 'content'],
			'getContents'         => ['content'],
			'getSize'             => [6],
			'tell'                => [0],
			'write'               => ['content', 6],
			'eof'                 => [false],
			'isReadable'          => [true],
			'isWritable'          => [true],
			'isSeekable'          => [true],
		];

		foreach ($mockMethods as $method => $arguments)
		{
			$return = array_pop($arguments);

			/** @var RendererInterface|\Mockery\MockInterface $mockRenderer */
			$mockRenderer = Mockery::mock('\\Joomla\\Renderer\\Renderer');
			/** @noinspection PhpMethodParametersCountMismatchInspection */
			$mockRenderer->shouldReceive($method)->once()->andReturn($return);

			$renderer = new EventDecorator($mockRenderer, new Dispatcher);
			$I->assertEquals($return, call_user_func_array([$renderer, $method], $arguments));
		}
	}

	public function DecoratorDelegatesAllVisitMethodsEmittingEvents(UnitTester $I)
	{
		$knownContentTypes = [
			Attribution::class => ['label', 'name'],
			Compound::class    => ['type', []],
			Headline::class    => ['text'],
			Paragraph::class   => ['text'],
		];

		foreach ($knownContentTypes as $className => $arguments)
		{
			if (count($arguments) == 2)
			{
				$content = new $className($arguments[0], $arguments[1]);
			}
			elseif (count($arguments) == 1)
			{
				$content = new $className($arguments[0]);
			}
			else
			{
				$content = new $className;
			}

			$contentType = preg_replace('~^.*\\\~', '', $className);
			$method      = 'visit' . $contentType;

			/** @var Dispatcher $mockDispatcher */
			$mockDispatcher = Mockery::mock(Dispatcher::class);
			$mockDispatcher->shouldReceive('dispatch')->once()->with(RenderContentTypeEvent::class);
			$mockDispatcher->shouldReceive('dispatch')->once()->with(RenderContentTypeSuccessEvent::class);

			/** @var RendererInterface $mockRenderer */
			$mockRenderer = Mockery::mock(Renderer::class);
			$mockRenderer->shouldReceive($method)->once()->andReturn(0);

			$renderer = new EventDecorator($mockRenderer, $mockDispatcher);

			/** @var ContentTypeInterface $content */
			$content->accept($renderer);
		}
	}
}
