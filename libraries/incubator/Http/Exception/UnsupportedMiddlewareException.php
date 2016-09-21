<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Exception;

/**
 * Thrown if an unsupported type of middleware is encountered.
 *
 * @package  Joomla/HTTP
 * @see      Joomla\Http\Application for supported types of middleware.
 * @since    __DEPLOY_VERSION__
 */
class UnsupportedMiddlewareException extends \LogicException
{
}
