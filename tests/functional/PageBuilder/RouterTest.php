<?php
/**
 * Part of the Joomla Framework PageBuilder Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Functional\PageBuilder;

use Joomla\Cms\ServiceProvider\CommandBusServiceProvider;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Http\Application;
use Joomla\Http\ServerRequestFactory;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryQuery;
use Joomla\ORM\Repository\RepositoryQueryHandler;
use Joomla\PageBuilder\DisplayPageCommand;
use Joomla\PageBuilder\RouterMiddleware;
use Joomla\Service\CommandBus;
use Joomla\Tests\DatabaseDataSet;
use Joomla\Tests\DatabaseTestCase;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
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

		$this->container      = new Container;
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
				'pages' => [
					[
						'id'  => 23,
						'url' => 'path/to/article',
					],
					[
						'id'  => 42,
						'url' => 'a/different/url',
					],
				],
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
	 * @testdox A known URL creates a DisplayPageCommand with the related ID
	 */
	public function testKnownUrlCreatesDisplayPageCommand($id, $url)
	{
		$test       = $this;
		$app        = new Application([
			new RouterMiddleware($this->container),
			function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($test, $id) {
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
