<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\UnitOfWork;

use Joomla\ORM\Exception\OrmException;

/**
 * Interface TransactionInterface
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
interface TransactionInterface
{
	/**
	 * Initiates a transaction.
	 *
	 * @return  void
	 * @throws  OrmException  on failure.
	 */
	public function beginTransaction();

	/**
	 * Commits a transaction.
	 *
	 * @return  void
	 * @throws  OrmException  on failure.
	 */
	public function commit();

	/**
	 * Rolls back the current transaction, as initiated by beginTransaction().
	 *
	 * @return  void
	 * @throws  OrmException  on failure.
	 */
	public function rollBack();
}
