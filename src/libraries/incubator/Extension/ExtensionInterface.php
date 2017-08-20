<?php
/**
 * Part of the Joomla Framework Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension;

use Joomla\Service\Query;
use Joomla\Service\QueryHandler;

/**
 * Extension Interface
 *
 * @package Joomla/Extension
 *
 * @since   1.0
 */
interface ExtensionInterface
{
    /**
     * @param   Query $query The query
     *
     * @return  QueryHandler[]
     */
    public function getQueryHandlers(Query $query);

    /**
     * Returns an array of callables to listen for events on a
     * DispatcherInterface.
     *
     * @param   string $eventName The name of the event
     *
     * @return  callable[]
     *
     * @see \Joomla\Event\DispatcherInterface::addListener
     * @see \Joomla\Event\DispatcherInterface::dispatch
     */
    public function getListeners($eventName);

    /**
     * @return string[]
     */
    public function getContentTypes();
}
