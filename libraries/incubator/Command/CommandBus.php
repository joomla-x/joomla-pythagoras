<?php
/**
 * Part of the Joomla Framework Command Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Command;

use Joomla\Di\Container;
use Joomla\Di\ContainerAwareTrait;

/**
 * Class CommandBus
 *
 * @package  Joomla/command
 *
 * @since    1.0
 */
class CommandBus
{
	use ContainerAwareTrait;

	/**
	 * CommandBus constructor.
	 *
	 * @param   array     $middleware  List of command middleware
	 * @param   Container $container   Dependency Injection Container
	 */
	public function __construct(array $middleware, Container $container)
	{
		$this->setContainer($container);
	}

	/**
	 * @param   CommandInterface $command The command
	 *
	 * @return  void
	 */
	public function handle(CommandInterface $command)
	{
	}
}
