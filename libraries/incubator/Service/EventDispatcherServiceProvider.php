<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Service;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\DI\ServiceProviderInterface;

class EventDispatcherServiceProvider implements ServiceProviderInterface
{

	public function register (Container $container)
	{
		$container->set('EventDispatcher', function  () {
			return new Dispatcher();
		}, true, true);
	}
}