<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Event;

use Doctrine\DBAL\Query\QueryBuilder;
use Joomla\Event\Event;

/**
 * Class QueryDatabaseEvent
 *
 * @package Joomla\ORM
 *
 * @since   __DEPLOY_VERSION__
 */
class QueryDatabaseEvent extends Event
{
	/**
	 * QueryDatabaseEvent constructor.
	 *
	 * @param   string       $entityClass The class of the entity
	 * @param   QueryBuilder $builder
	 */
	public function __construct($entityClass, QueryBuilder $builder)
	{
		parent::__construct(
			'onQueryDatabase',
			[
				'entityClass' => $entityClass,
				'query'       => $builder
			]
		);
	}

	/**
	 * @return   string
	 */
	public function getEntityClass()
	{
		return $this->getArgument('entityClass');
	}

	/**
	 * @return   QueryBuilder
	 */
	public function getQuery()
	{
		return $this->getArgument('query');
	}
}
