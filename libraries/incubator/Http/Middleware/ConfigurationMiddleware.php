<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Dotenv\Dotenv;
use Joomla\Http\MiddlewareInterface;
use Joomla\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Load the Configuration Data.
 *
 * The configuration is read from a file in the directory passed to the constructor (defaults to `.env`).
 * It is wrapped into a Registry object and added to the dependency injection container with the key `config`.
 *
 * Access the configuration in subsequent middleware:
 *
 *     $container = $request->getAttribute('container');
 *     $config    = $container->get('config');
 *
 * @package  Joomla/HTTP
 *
 * @since    1.0
 */
class ConfigurationMiddleware implements MiddlewareInterface
{
	/** @var string Path to `.env` file */
	private $path;

	/** @var string Name of the `.env` file */
	private $file;

	/**
	 * ConfigurationMiddleware constructor.
	 *
	 * @param   string  $path  Path to `.env` file
	 * @param   string  $file  Name of the `.env` file
	 */
	public function __construct($path, $file = '.env')
	{
		$this->path = $path;
		$this->file = $file;
	}

	/**
	 * Execute the middleware. Don't call this method directly; it is used by the `Application` internally.
	 *
	 * @internal
	 *
	 * @param   ServerRequestInterface $request  The request object
	 * @param   ResponseInterface      $response The response object
	 * @param   callable               $next     The next middleware handler
	 *
	 * @return  ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$dotenv = new Dotenv($this->path, $this->file);
		$dotenv->overload();

		$container = $request->getAttribute('container');
		$container->set('config', new Registry($_ENV), true);

		if (!defined('JPATH_ROOT'))
		{
			define('JPATH_ROOT', $container->get('config')->get('JPATH_ROOT', $this->path));
		}

		return $next($request, $response);
	}
}
