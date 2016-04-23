<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\J3Compatibility\Http\Middleware;

use Joomla\Http\MiddlewareInterface;
use Joomla\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package  Joomla/J3Compatibility
 *
 * @since    1.0
 */
class RouterMiddleware implements MiddlewareInterface
{
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
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
	{
		$attributes = $request->getAttributes();

		if (!isset($attributes['command']) && $this->isLegacy())
		{
			// @todo Emit afterRouting event
		}

		return $next($request, $response);
	}

	/**
	 * Check if the requested option belongs to a legacy component
	 *
	 * @return boolean
	 */
	private function isLegacy()
	{
		return isset($_REQUEST['option']) && preg_match('~^com_~', $_REQUEST['option']);
	}
}
