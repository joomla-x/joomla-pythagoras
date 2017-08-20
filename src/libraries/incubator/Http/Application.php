<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http;

use Joomla\Http\Exception\UnsupportedMiddlewareException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Application
 *
 * @package  Joomla/HTTP
 *
 * @since    __DEPLOY_VERSION__
 */
class Application
{
    /** @var MiddlewareInterface[] */
    private $stack;

    /**
     * Application constructor.
     *
     * @param   MiddlewareInterface[] $stack The middleware stack
     */
    public function __construct(array $stack = [])
    {
        $this->stack = $stack;
    }

    /**
     * @param   ServerRequestInterface $request The request object
     *
     * @return  ResponseInterface
     */
    public function run(ServerRequestInterface $request)
    {
        return $this->buildCallChain()->__invoke($request, new Response);
    }

    /**
     * @return  \Closure
     */
    private function buildCallChain()
    {
        $next = function ($request, $response) {
            return $response;
        };

        foreach (array_reverse($this->stack) as $middleware) {
            if ($middleware instanceof MiddlewareInterface) {
                $next = function ($request, $response) use ($middleware, $next) {
                    return $middleware->handle($request, $response, $next);
                };
            } elseif ($middleware instanceof \Closure) {
                $next = function ($request, $response) use ($middleware, $next) {
                    return $middleware($request, $response, $next);
                };
            } elseif (is_callable($middleware)) {
                $next = function ($request, $response) use ($middleware, $next) {
                    return call_user_func($middleware, $request, $response, $next);
                };
            } else {
                throw new UnsupportedMiddlewareException(
                    "HTTP middleware of type '" . get_class($middleware) . "' is not supported"
                );
            }
        }

        return $next;
    }
}
