<?php
/**
 * Part of the Joomla Framework Command Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Command\Middleware;

use Joomla\Di\ContainerAwareTrait;

/**
 * Class DynamicMiddleware
 *
 * DynamicMiddleware looks into the database for installed middleware extensions
 * and executes them in asc order before calling `$next`, and in desc order after that.
 *
 * @package joomla/command
 *
 * @since  1.0
 */
class DynamicMiddleware
{
    use ContainerAwareTrait;
}
