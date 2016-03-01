<?php
/**
 * Part of the Joomla Framework Command Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Command;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Command
 *
 * @package  Joomla/command
 *
 * @since    1.0
 */
class Command implements CommandInterface
{
	/**
	 * @param   ServerRequestInterface $request   The request object
	 * @param   ResponseInterface      $response  The response object
	 * @param   ContainerInterface     $container The Dependency Injection Container
	 *
	 * @return  Command
	 */
	public static function fromRequest(ServerRequestInterface $request, ResponseInterface $response, ContainerInterface $container)
	{
		return new self;
	}
}