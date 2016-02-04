<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Joomla\Http\MiddlewareInterface;
use Joomla\Renderer\Factory as RendererFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Replaces the response body with a renderer.
 *
 * The renderer is chosen to be suitable for the current request.
 * If the request does not specify a preferred mimetype, `text/plain` is rendered.
 *
 * @package joomla/renderer
 *
 * @since  1.0
 */
class RendererMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $acceptHeader = $request->getHeaderLine('Accept');

        if (empty($acceptHeader)) {
            $acceptHeader = 'text/plain';
        }

        $renderer = (new RendererFactory)->create($acceptHeader);

        return $response->withBody($renderer);
    }
}
