<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\MapStrategy;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Exception\RepositoryNotFoundException;
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
		$map     = parse_ini_file(JPATH_ROOT . '/config/entities.ini');
		$locator = new Locator(
			[
				new MapStrategy(JPATH_ROOT, $map),
				new RecursiveDirectoryStrategy(JPATH_ROOT . '/extensions'),
			]
		);

		$builder = new EntityBuilder($locator);
		$builder->setDispatcher($this->getDispatcher());
		$repository = new Repository($query->entityName, $builder);

		if (is_null($repository))
		{
			throw new RepositoryNotFoundException("Unable to create the '$query->entityName' repository.");
		}

		return $repository;
	}
}
