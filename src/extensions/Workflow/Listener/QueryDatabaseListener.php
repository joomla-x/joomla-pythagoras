<?php
/**
 * Part of the Joomla! Category Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Workflow\Listener;

use Doctrine\DBAL\Query\QueryBuilder;
use Joomla\Extension\Article\Entity\Article;
use Joomla\ORM\Event\QueryDatabaseEvent;

/**
 * Class QueryDatabaseListener
 *
 * @package Joomla\Extension\Workflow
 *
 * @since   1.0
 */
class QueryDatabaseListener
{
	/** @var integer[] List of allowed states. */
	private $allowedStates = [1];

	/**
	 * Event handler
	 *
	 * @param   QueryDatabaseEvent $event The event
	 *
	 * @return  void
	 */
	public function addStateConstraint(QueryDatabaseEvent $event)
	{
		$entityClass = $event->getEntityClass();
		$query       = $event->getQuery();

		if (!$this->hasWorkflow($entityClass) || empty($this->allowedStates))
		{
			return;
		}

		$entityAlias = 'a';
		$mapAlias = $this->getNextAlias($query);

		$query->innerJoin($entityAlias, 'states_entities', $mapAlias, "{$entityAlias}.id={$mapAlias}.entity_id");

		if (count($this->allowedStates) == 1)
		{
			$query
				->andWhere("{$mapAlias}.state_id=" . $query->createPositionalParameter(reset($this->allowedStates)));

			return;
		}

		$states = [];

		foreach ($this->allowedStates as $state)
		{
			$states[] = $query->createPositionalParameter($state);
		}

		$query
			->andWhere("{$mapAlias}.state_id IN (" . implode(',', $states) . ")");
	}

	/**
	 * @param   integer[]  $states  List of (IDs of) allowed states. An empty array marks all states allowed.
	 *
	 * @return  void
	 */
	public function allowStates(array $states)
	{
		$this->allowedStates = $states;
	}

	/**
	 * @param   string  $entityClass  The entity class
	 *
	 * @return  boolean
	 */
	private function hasWorkflow($entityClass)
	{
		return $entityClass == Article::class;
	}

	/**
	 * @param   QueryBuilder  $query  The query builder
	 *
	 * @return  string
	 */
	private function getNextAlias($query)
	{
		if (!preg_match_all('~\b(\w+)\.~', $query->getSQL(), $matches))
		{
			return 'b';
		}

		$aliases = array_unique($matches[1]);

		for ($alias = 'b'; in_array($alias, $aliases); $alias++)
		{
			continue;
		}

		return $alias;
	}
}
