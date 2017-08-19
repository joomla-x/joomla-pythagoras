<?php
/**
 * Part of the Joomla Framework Renderer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Renderer;

use Joomla\Renderer\Exception\NotFoundException;
use Joomla\Renderer\Factory;
use Joomla\Renderer\HtmlRenderer;
use Joomla\Renderer\JsonRenderer;
use Joomla\Renderer\PlainRenderer;
use Joomla\Renderer\XmlRenderer;
use Joomla\Tests\Unit\Renderer\Mock\ArbitraryInteropContainer;
use UnitTester;

class RendererFactoryCest
{
	/** @var  Factory */
	private $factory;

	public function _before(UnitTester $I)
	{
		$this->factory = new Factory([
			'text/plain'       => PlainRenderer::class,
			'text/html'        => HtmlRenderer::class,
			'application/xml'  => XmlRenderer::class,
			'application/json' => JsonRenderer::class,
		]);
	}

	public function _after(UnitTester $I)
	{
	}

	private function dataAcceptHeaders()
	{
		$acceptHeaders = array(
			[
				'Accept: text/*;q=0.3, text/html;q=0.7, text/html;level=1, text/html;level=2;q=0.4, */*;q=0.5',
				HtmlRenderer::class
			],
			['Accept: audio/*; q=0.2, audio/basic', NotFoundException::class],
			['text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c', HtmlRenderer::class],
			['application/xml', XmlRenderer::class],
			['application/xml;q=0.8, application/json; q=0.9', JsonRenderer::class],
			['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', HtmlRenderer::class],
		);

		return $acceptHeaders;
	}

	/**
	 * @testdox RendererFactory creates the correct Renderer
	 */
	public function RendererFactoryCreatesTheCorrectRenderer(UnitTester $I)
	{
		$container = new ArbitraryInteropContainer();

		foreach ($this->dataAcceptHeaders() as $foo)
		{
			list($acceptHeader, $expected) = $foo;

			try
			{
				$renderer = $this->factory->create($acceptHeader, $container);
				$I->assertTrue(
					$renderer instanceof $expected,
					"Expected $expected, but got " . get_class($renderer)
				);
			}
			catch (NotFoundException $e)
			{
				if (!($e instanceof $expected))
				{
					$I->fail('Expected NotFoundException, got ' . get_class($e));
				}
			}
		}
	}
}
