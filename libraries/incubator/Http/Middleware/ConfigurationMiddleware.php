<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Dotenv\Dotenv;
use Joomla\Di\Container;
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
 * @package joomla/http
 *
 * @since  1.0
 */
class ConfigurationMiddleware implements MiddlewareInterface
{
    private $path;

    private $file;

    /**
     * ConfigurationMiddleware constructor.
     *
     * @param   string $path Path to `.env` file
     */
    public function __construct($path, $file = '.env')
    {
        $this->path = $path;
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $dotenv = new Dotenv($this->path, $this->file);
        $dotenv->overload();

        /** @var Container $container */
        $container = $request->getAttribute('container');
        $container->set('config', new Registry($_ENV), true);

        return $next($request, $response);
    }
}
