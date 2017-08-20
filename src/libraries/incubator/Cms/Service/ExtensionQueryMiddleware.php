<?php
/**
 * Part of the Joomla! Cms Service Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Service;

use Joomla\Extension\ExtensionFactoryInterface;
use Joomla\Service\Query;
use League\Tactician\Middleware;

/**
 * ExtensionQueryMiddleware
 *
 * @package Joomla/Cms/Service
 *
 * @since   1.0
 */
class ExtensionQueryMiddleware implements Middleware
{
	/** @var ExtensionFactoryInterface  The extension factory */
	private $extensionFactory = null;

	/**
	 * ExtensionQueryMiddleware constructor.
	 *
	 * @param   ExtensionFactoryInterface $extensionFactory  The extension factory
	 */
	public function __construct(ExtensionFactoryInterface $extensionFactory)
	{
		$this->extensionFactory = $extensionFactory;
	}

	/**
	 * @param   object   $command The command
	 * @param   callable $next    The next middleware
	 *
	 * @return  array
	 */
	public function execute($command, callable $next)
	{
		if ($command instanceof Query)
		{
			$return = [];

			foreach ($this->extensionFactory->getExtensions() as $extension)
			{
				foreach ($extension->getQueryHandlers($command) as $handler)
				{
					$return[] = $handler->handle($command);
				}
			}

			if ($return)
			{
				return $return;
			}
		}

		return $next($command);
	}
}
