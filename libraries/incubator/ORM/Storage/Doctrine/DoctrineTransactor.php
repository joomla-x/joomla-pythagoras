<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\UnitOfWork\TransactionInterface;

/**
 * Class DoctrineTransactor
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class DoctrineTransactor implements TransactionInterface
{
	/** @var Connection the connection to work on */
	private $connection = null;

	/**
	 * DoctrineTransactor constructor.
	 *
	 * @param   Connection $connection The database connection
	 */
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Initiates a transaction.
	 *
	 * @return  void
	 * @throws  OrmException  on failure.
	 */
	public function beginTransaction()
	{
		try
		{
			$this->connection->beginTransaction();
		}
		catch (DBALException $e)
		{
			throw new OrmException("Unable to start transaction.\n" . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Commits a transaction.
	 *
	 * @return  void
	 * @throws  OrmException  on failure.
	 */
	public function commit()
	{
		try
		{
			$this->connection->commit();
		}
		catch (DBALException $e)
		{
			throw new OrmException("Unable to commit changes.\n" . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Rolls back the current transaction, as initiated by beginTransaction().
	 *
	 * @return  void
	 * @throws  OrmException  on failure.
	 */
	public function rollBack()
	{
		try
		{
			$this->connection->rollBack();
		}
		catch (DBALException $e)
		{
			throw new OrmException("Unable to start transaction.\n" . $e->getMessage(), 0, $e);
		}
	}
}
