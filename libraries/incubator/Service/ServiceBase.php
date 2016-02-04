<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\DI\Container;

/**
 * Base class for Services.
 *
 * @since  __DEPLOY__
 */
class ServiceBase implements Service
{
	// Dependency injection container.
	private $container = null;

	/**
	 * Constructor.
	 *
	 * @param   Container  $container  A dependency injection container
	 *
	 * @since   __DEPLOY__
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Handle a command or query.
	 *
	 * @param   object  $command  A Command or Query object.
	 *
	 * @return  boolean
	 *
	 * @throws  \InvalidArgumentException
	 * @since   __DEPLOY__
	 */
	public function execute($command)
	{
		if ($command instanceof Command)
		{
			$this->container->get('commandbus')->handle($command);

			return true;
		}

		if ($command instanceof Query)
		{
			return $this->container->get('querybus')->handle($command);
		}

		throw new \InvalidArgumentException('Argument must be a Command or a Query');
	}
}
