<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\Event\DispatcherInterface;
use Joomla\Event\DispatcherAwareTrait;

/**
 * Abstract base class for query handlers.
 *
 * @package  Joomla/Service
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class QueryHandler
{
	use DispatcherAwareTrait;

	/** @var CommandBus The message bus */
	protected $messageBus = null;

	/**
	 * Constructor.
	 *
	 * @param   CommandBus           $messageBus  A command bus
	 * @param   DispatcherInterface  $dispatcher  A dispatcher
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(CommandBus $messageBus, DispatcherInterface $dispatcher)
	{
		$this->messageBus = $messageBus;
		$this->setDispatcher($dispatcher);
	}

	/**
	 * Get the command bus.
	 *
	 * @return   CommandBus
	 */
	public function getCommandBus()
	{
		return $this->messageBus;
	}
}
