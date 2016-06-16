<?php
/**
 * Part of the Joomla Framework Renderer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Renderer\Exception\NotFoundException;
use Joomla\Tests\Unit\Renderer\Mock\Content;
use Joomla\Tests\Unit\Renderer\Mock\ContentType;
use Joomla\Tests\Unit\Renderer\Mock\NewContentType;
use Joomla\Tests\Unit\Renderer\Mock\OtherContentType;
use Joomla\Tests\Unit\Renderer\Mock\Renderer;
use Joomla\Tests\Unit\Renderer\Mock\UnregisteredContentType;
use UnitTester;

class DynamicRendererCest
{
	public function _before(UnitTester $I)
	{
	}

	public function _after(UnitTester $I)
	{
	}

	/**
	 * @testdox Callbacks can be used to render custom content types
	 */
	public function DynamicRendererUsesCallbacks(UnitTester $I)
	{
		require_once __DIR__ . '/Mock/Content.php';

		$renderer = new Renderer(['token' => '*/*']);

		// Static method
		$renderer->registerContentType('NewContent', [NewContentType::class, 'asHtml']);

		// Dynamic method
		$renderer->registerContentType('OtherContent', 'asHtml');

		// Callable
		$renderer->registerContentType('Default', function (Content $content)
		{
			return 'default: ' . $content->getContents() . "\n";
		});

		/** @var ContentTypeInterface[] $content */
		$content = array(
			new ContentType('ContentType'),
			new NewContentType('NewContentType'),
			new OtherContentType('OtherContentType'),
			new UnregisteredContentType('UnregisteredContentType'),
		);

		foreach ($content as $c)
		{
			$c->accept($renderer);
		}

		$I->assertEquals(
			"standard: ContentType\n" .
			"static: NewContentType\n" .
			"dynamic: OtherContentType\n" .
			"default: UnregisteredContentType\n",
			$renderer->getContents()
		);
	}

	/**
	 * @testdox Exception is thrown, if an unknown content type is encountered, and no default is provided
	 */
	public function DynamicRendererThrowsExceptionOnMissingCallback(UnitTester $I)
	{
		require_once __DIR__ . '/Mock/Content.php';

		$renderer = new Renderer(['token' => '*/*']);

		$content = new UnregisteredContentType('UnregisteredContentType');

		try
		{
			$content->accept($renderer);
			$I->fail('Expected NotFoundException was not thrown');
		}
		catch (\Exception $e)
		{
			$I->assertTrue($e instanceof NotFoundException);
		}
	}
}
