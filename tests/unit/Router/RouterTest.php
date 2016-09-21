<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Router\Tests;

use Joomla\Router\Router;

/**
 * Tests for the Joomla\Router\Router class.
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * An instance of the object to be tested.
	 *
	 * @var  Router
	 */
	protected $instance;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = new Router;
	}

	/**
	 * @testdox  Ensure the Router is instantiated correctly with no injected routes.
	 *
	 * @covers   \Joomla\Router\Router::__construct
	 */
	public function test__construct()
	{
		$emptyRoutes = [
			'GET' => [],
			'PUT' => [],
			'POST' => [],
			'DELETE' => [],
			'HEAD' => [],
			'OPTIONS' => [],
			'TRACE' => [],
			'PATCH' => []
		];

		$router = new Router;

		$this->assertAttributeEquals(
			$emptyRoutes,
			'routes',
			$router,
			'A Router should have no known routes by default.'
		);
	}

	/**
	 * @testdox  Ensure the Router is instantiated correctly with injected routes.
	 *
	 * @covers   \Joomla\Router\Router::__construct
	 * @uses     \Joomla\Router\Router::addRoutes
	 */
	public function test__constructNotEmpty()
	{
		$routes = [
			[
				'pattern' => 'login',
				'controller' => 'login'
			],
			[
				'pattern' => 'requests/:request_id',
				'controller' => 'request',
				'rules' => [
					'request_id' => '(\d+)'
				]
			]
		];

		$rules = [
			'GET' => [
				[
					'regex' => chr(1) . '^login$' . chr(1),
					'vars' => [],
					'controller' => 'login'
				],
				[
					'regex' => chr(1) . '^requests/((\d+))$' . chr(1),
					'vars' => ['request_id'],
					'controller' => 'request'
				]
			],
			'PUT' => [],
			'POST' => [],
			'DELETE' => [],
			'HEAD' => [],
			'OPTIONS' => [],
			'TRACE' => [],
			'PATCH' => []
		];

		$router = new Router($routes);

		$this->assertAttributeEquals(
			$rules,
			'routes',
			$router,
			'When passing an array of routes when instantiating a Router, the maps property should be set accordingly.'
		);
	}

	/**
	 * @testdox  Ensure a route is added to the Router.
	 *
	 * @covers   \Joomla\Router\Router::addRoute
	 * @uses     \Joomla\Router\Router::buildRegexAndVarList
	 */
	public function testAddRoute()
	{
		$this->instance->addRoute('GET', 'foo', 'MyApplicationFoo');

		$this->assertAttributeEquals(
			[
				'GET' => [
					[
						'regex' => chr(1) . '^foo$' . chr(1),
						'vars' => [],
						'controller' => 'MyApplicationFoo'
					]
				],
				'PUT' => [],
				'POST' => [],
				'DELETE' => [],
				'HEAD' => [],
				'OPTIONS' => [],
				'TRACE' => [],
				'PATCH' => []
			],
			'routes',
			$this->instance
		);
	}

	/**
	 * @testdox  Ensure several routes are added to the Router.
	 *
	 * @covers   \Joomla\Router\Router::addRoutes
	 * @uses     \Joomla\Router\Router::addRoute
	 */
	public function testAddRoutes()
	{
		$routes = [
			[
				'pattern' => 'login',
				'controller' => 'login'
			],
			[
				'pattern' => 'user/:name/:id',
				'controller' => 'UserController',
				'rules' => [
					'id' => '(\d+)'
				]
			],
			[
				'pattern' => 'requests/:request_id',
				'controller' => 'request',
				'rules' => [
					'request_id' => '(\d+)'
				]
			]
		];

		$rules = [
			'GET' => [
				[
					'regex' => chr(1) . '^login$' . chr(1),
					'vars' => [],
					'controller' => 'login'
				],
				[
					'regex' => chr(1) . '^user/([^/]*)/((\d+))$' . chr(1),
					'vars' => [
						'name',
						'id'
					],
					'controller' => 'UserController'
				],
				[
					'regex' => chr(1) . '^requests/((\d+))$' . chr(1),
					'vars' => ['request_id'],
					'controller' => 'request'
				]
			],
			'PUT' => [],
			'POST' => [],
			'DELETE' => [],
			'HEAD' => [],
			'OPTIONS' => [],
			'TRACE' => [],
			'PATCH' => []
		];

		$this->instance->addRoutes($routes);
		$this->assertAttributeEquals($rules, 'routes', $this->instance);
	}

	/**
	 * @testdox  Ensure the Router parses routes.
	 *
	 * @param   string   $r  The route to parse.
	 * @param   boolean  $e  True if an exception is expected.
	 * @param   array    $i  The expected return data.
	 * @param   boolean  $m  True if routes should be set up.
	 *
	 * @covers        \Joomla\Router\Router::parseRoute
	 * @dataProvider  seedTestParseRoute
	 * @uses          \Joomla\Router\Router::addRoutes
	 */
	public function testParseRoute($r, $e, $i, $m)
	{
		if ($m)
		{
			$this->setRoutes();
		}

		// If we should expect an exception set that up.
		if ($e)
		{
			$this->expectException('InvalidArgumentException');
		}

		// Execute the route parsing.
		$actual = $this->instance->parseRoute($r);

		// Test the assertions.
		$this->assertEquals($i, $actual, 'Incorrect value returned.');
	}

	/**
	 * Provides test data for the testParseRoute method.
	 *
	 * @return  array
	 */
	public static function seedTestParseRoute()
	{
		// Route Pattern, Throws Exception, Return Data, MapSetup
		return [
			['', true, [], false],
			['articles/4', true, [], false],
			['', false, ['controller' => 'DefaultController', 'vars' => []], true],
			['login', false, ['controller' => 'LoginController', 'vars' => []], true],
			['articles', false, ['controller' => 'ArticlesController', 'vars' => []], true],
			['articles/4', false, ['controller' => 'ArticleController', 'vars' => ['article_id' => 4]], true],
			['articles/4/crap', true, [], true],
			['test', true, [], true],
			['test/foo', true, [], true],
			['test/foo/path', true, [], true],
			['test/foo/path/bar', false, ['controller' => 'TestController', 'vars' => ['seg1' => 'foo', 'seg2' => 'bar']], true],
			['content/article-1/*', false, ['controller' => 'ContentController', 'vars' => []], true],
			[
				'content/cat-1/article-1',
				false,
				['controller' => 'ArticleController', 'vars' => ['category' => 'cat-1', 'article' => 'article-1']],
				true
			],
			[
				'content/cat-1/cat-2/article-1',
				false,
				['controller' => 'ArticleController', 'vars' => ['category' => 'cat-1/cat-2', 'article' => 'article-1']],
				true
			],
			[
				'content/cat-1/cat-2/cat-3/article-1',
				false,
				['controller' => 'ArticleController', 'vars' => ['category' => 'cat-1/cat-2/cat-3', 'article' => 'article-1']],
				true
			]
		];
	}

	/**
	 * Setup the router with routes.
	 *
	 * @return  void
	 */
	protected function setRoutes()
	{
		$this->instance->addRoutes(
			[
				[
				   'pattern' => 'login',
				   'controller' => 'LoginController'
				],
				[
				   'pattern' => 'logout',
				   'controller' => 'LogoutController'
				],
				[
				   'pattern' => 'articles',
				   'controller' => 'ArticlesController'
				],
				[
				   'pattern' => 'articles/:article_id',
				   'controller' => 'ArticleController'
				],
				[
				   'pattern' => 'test/:seg1/path/:seg2',
				   'controller' => 'TestController'
				],
				[
				   'pattern' => 'content/:/\*',
				   'controller' => 'ContentController'
				],
				[
				   'pattern' => 'content/*category/:article',
				   'controller' => 'ArticleController'
				],
				[
					'pattern' => '/',
					'controller' => 'DefaultController'
				]
			]
		);
	}
}
