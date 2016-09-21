<?php
/**
 * Part of the Joomla Framework PageBuilder Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Functional\PageBuilder;

use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Http\Application;
use Joomla\Http\ServerRequestFactory;
use Joomla\ORM\Repository\RepositoryQuery;
use Joomla\ORM\Repository\RepositoryQueryHandler;
use Joomla\PageBuilder\DisplayPageCommand;
use Joomla\PageBuilder\RouterMiddleware;
use Joomla\Service\CommandBus;
use Joomla\Tests\DatabaseDataSet;
use Joomla\Tests\DatabaseTestCase;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RouterTest
 *
 * @package Joomla\Tests\Functional\PageBuilder
 */
class RouterTest extends DatabaseTestCase
{
	/** @var  Container */
	private $container;

	public function setUp()
	{
		$repository = (new RepositoryQueryHandler(new CommandBus([]), new Dispatcher))->handle(new RepositoryQuery('page'));

		$this->container = new Container;
		$this->container->set('PageRepository', $repository, true, true);
	}

	/**
	 * Returns the test dataset.
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	protected function getDataSet()
	{
		return new DatabaseDataSet(
			[
				'masters'        => [
					[
						'id'          => 1,
						'title'       => 'default', // A decriptive name for the master layout
						'template_id' => 1 // A reference to the template used by this master layout
					]
				],
				'master_content' => [
					[
						'id'        => 1,
						'master_id' => 1,
						'position'  => '', // The position, similar to J!3's module position
						'ordering'  => 1, // Order of this element within this position
						'component' => 'content', // Component to provide the content
						'layout'    => 'default', // The layout for the content
						'reference' => null, // If related to another content item on this page, this is a pointer to it (format position:ordering)
						'selection' => 'id=1', // The selection criteria. Related content item is addressed as 'ref'
						'params'    => '{}' // JSON encoded parameters
					]
				],
				'pages'          => [
					[
						'id'        => 23,
						'url'       => 'path/to/article', // The URL for this page. May contain variables, denoted with leading colon
						'title'     => 'An Article', // The label for the menu item
						'parent_id' => null, // Parent menu item
						'master_id' => 1 // Master to use as a base for this page
					],
					[
						'id'        => 42,
						'url'       => 'a/different/url', // The URL for this page. May contain variables, denoted with leading colon
						'title'     => 'Another Article', // The label for the menu item
						'parent_id' => null, // Parent menu item
						'master_id' => 1 // Master to use as a base for this page
					]
				],
				'page_content'   => [
					[
						'id'        => 1,
						'page_id'   => 23,
						'position'  => 'main', // The position, similar to J!3's module position
						'ordering'  => 1, // Order of this element within this position
						'component' => 'content', // Component to provide the content
						'layout'    => 'default', // The layout for the content
						'reference' => null, // If related to another content item on this page, this is a pointer to it (format position:ordering)
						'selection' => 'id=1', // The selection criteria. Related content item is addressed as 'ref'
						'params'    => '{}' // JSON encoded parameters
					]
				]
			]
		);
	}

	/**
	 * @return array
	 */
	public function provideRouterData()
	{
		return [
			/* ID, URL */
			[23, '/path/to/article'],
			[42, '/a/different/url']
		];
	}

	/**
	 * @dataProvider provideRouterData
	 * @testdox      A known URL creates a DisplayPageCommand with the related ID
	 */
	public function testKnownUrlCreatesDisplayPageCommand($id, $url)
	{
		$test = $this;
		$app  = new Application([
			new RouterMiddleware($this->container),
			function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($test, $id)
			{
				$attributes = $request->getAttributes();

				$command = $attributes['command'];
				$test->assertInstanceOf(DisplayPageCommand::class, $command);
				$test->assertEquals($id, $command->id);

				return $next($request, $response);
			}
		]);

		$server = [
			'REQUEST_URI' => $url,
		];

		$app->run(ServerRequestFactory::fromGlobals($server));
	}

	/**
	 * @testdox The page record is cached in the repository
	 */
	public function testPageRecordIsCached()
	{
	}
}
