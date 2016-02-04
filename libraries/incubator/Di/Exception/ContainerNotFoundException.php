<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Di\Exception;

/**
 * No container is available.
 *
 * @package joomla/di
 *
 * @since  2.0
 */
class ContainerNotFoundException extends \RuntimeException implements \Interop\Container\Exception\ContainerException
{
}
