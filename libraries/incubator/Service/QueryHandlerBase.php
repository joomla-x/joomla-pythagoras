<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\DI\Container;

/**
 * Abstract base class for query/service handlers.
 *
 * @since  __DEPLOY__
 */
class QueryHandlerBase implements QueryHandler
{
	/** @var Container  Container */
	protected $container = null;

	/**
	 * Constructor.
	 *
	 * @param   Container $container A DI container.
	 *
	 * @since   __DEPLOY__
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}
}
