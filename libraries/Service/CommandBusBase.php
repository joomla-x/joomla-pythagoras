<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

/**
 * Request Bus proxy class.
 * 
 * This is just a proxy to the actual command bus implementation.
 * The League Tactician command bus currently proxied requires PHP 5.5 minimum
 * and so cannot be used across all Joomla 3.x sites.  This needs to be
 * resolved before release.
 * 
 * @since  __DEPLOY__
 */
class CommandBusBase implements CommandBus
{
	private $commandBus = null;

	/**
	 * Constructor.
	 * 
	 * @param   array  $middleware  Array of middleware decorators.
	 * 
	 * @since   __DEPLOY__
	 */
	public function __construct(array $middleware)
	{
		$this->commandBus = new \League\Tactician\CommandBus($middleware);
	}

	/**
	 * Handle a command.
	 * 
	 * @param   Command  $command  A command object.
	 * 
	 * @return  mixed
	 * 
	 * @since   __DEPLOY__
	 */
	public function handle(Command $command)
	{
		return $this->commandBus->handle($command);
	}
}
