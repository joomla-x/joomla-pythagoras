<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Di\Exception;

use Interop\Container\Exception\ContainerException;

/**
 * Exception class for handling errors in resolving a dependency
 *
 * @package  Joomla/DI
 *
 * @since    1.0
 */
class DependencyResolutionException extends \RuntimeException implements ContainerException
{
}
