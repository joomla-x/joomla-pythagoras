<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Repository;

use Joomla\Service\Query;

/**
 * Repository Query
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class RepositoryQuery extends Query
{
	/**
	 * RepositoryQuery constructor.
	 *
	 * @param   string  $entityName  The name of the requested entity
	 */
	public function __construct($entityName)
	{
		$this->entityName = $entityName;

		parent::__construct();
	}
}
