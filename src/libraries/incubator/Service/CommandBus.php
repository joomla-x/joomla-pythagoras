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
 * Command Bus proxy class.
 *
 * This is just a proxy to the actual command bus implementation.
 * The League Tactician command bus currently proxied requires PHP 5.5 minimum
 * and so cannot be used across all Joomla 3.x sites.  This needs to be
 * resolved before release.
 *
 * @package  Joomla/Service
 *
 * @since    __DEPLOY_VERSION__
 */
class CommandBus
{
    /** @var \League\Tactician\CommandBus The command bus */
    private $commandBus = null;

    /**
     * Constructor.
     *
     * @param   array $middleware Array of middleware decorators.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function __construct(array $middleware)
    {
        $this->commandBus = new \League\Tactician\CommandBus($middleware);
    }

    /**
     * Handle a command or query.
     *
     * @param   Message $message A command object.
     *
     * @return  mixed
     *
     * @since   __DEPLOY_VERSION__
     */
    public function handle(Message $message)
    {
        return $this->commandBus->handle($message);
    }
}
