<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\Service\QueryHandler;
use Joomla\ORM\Entity\EntityBuilder;

/**
 * Class Repository
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class RepositoryQueryHandler extends QueryHandler
{
	/**
	 * Handle the query.
	 *
	 * @param   RepositoryQuery  $query  The query
	 *
	 * @return  Repository
	 */
	public function handle(RepositoryQuery $query)
	{
		$strategies = [
			new RecursiveDirectoryStrategy(JPATH_ROOT . '/extensions')
		];
		$locator    = new Locator($strategies);

		$builder = new EntityBuilder($locator);
		$builder->setDispatcher($this->getDispatcher());
		return new Repository($query->entityName, $builder);
	}
}
