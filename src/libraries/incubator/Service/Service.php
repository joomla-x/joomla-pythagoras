<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

/**
 * Abstract service layer class.
 *
 * @package  Joomla/Service
 *
 * @since    __DEPLOY_VERSION__
 */
abstract class Service
{
    /** @var CommandBus The command bus */
    protected $commandBus = null;

    /**
     * Constructor.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function __construct()
    {
        // Get the command bus buidler.
        $commandBusBuilder = new CommandBusBuilder;

        // Build the command bus.
        $this->commandBus = $commandBusBuilder->getCommandBus();
    }

    /**
     * Command/query handler.
     *
     * @param   Message $message A Command or a Query.
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function handle(Message $message)
    {
        return $this->commandBus->handle($message);
    }
}
