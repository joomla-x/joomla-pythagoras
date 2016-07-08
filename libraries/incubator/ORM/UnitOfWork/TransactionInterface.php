<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\UnitOfWork;

use Joomla\ORM\Exception\OrmException;

interface TransactionInterface
{
	/**
	 * Initiates a transaction.
	 *
	 * @throws OrmException on failure.
	 */
	public function beginTransaction();

	/**
	 * Commits a transaction.
	 *
	 * @throws OrmException on failure.
	 */
	public function commit();

	/**
	 * Rolls back the current transaction, as initiated by beginTransaction().
	 *
	 * @throws OrmException on failure.
	 */
	public function rollBack();
}
