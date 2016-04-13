<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\Dispatcher;

class EventDispatcherServiceProvider implements ServiceProviderInterface
{

	private $key = 'EventDispatcher';

	public function register(Container $container, $alias = null)
	{
		$container->set($this->key, function ()
		{
			return new Dispatcher();
		}, true, true);

		if (!empty($alias))
		{
			$container->alias($alias, $this->key);
		}
	}
}
