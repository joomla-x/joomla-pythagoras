<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Storage\Csv;

use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\UnitOfWork\TransactionInterface;

/**
 * Class CsvTransactor
 *
 * @package Joomla/ORM
 *
 * @since   1.0
 */
class CsvTransactor implements TransactionInterface
{
	/** @var CsvDataGateway  */
	private $gateway;

	/**
	 * CsvTransactor constructor.
	 *
	 * @param CsvDataGateway $gateway
	 */
	public function __construct(CsvDataGateway $gateway)
	{
		$this->gateway = $gateway;
	}

	/**
	 * Initiates a transaction.
	 *
	 * @throws OrmException on failure.
	 */
	public function beginTransaction()
	{
		try
		{
			$this->gateway->beginTransaction();
		}
		catch (\RuntimeException $e)
		{
			throw new OrmException("Unable to start transaction.\n" . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Commits a transaction.
	 *
	 * @throws OrmException on failure.
	 */
	public function commit()
	{
		try
		{
			$this->gateway->commit();
		}
		catch (\RuntimeException $e)
		{
			throw new OrmException("Unable to commit changes.\n" . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Rolls back the current transaction, as initiated by beginTransaction().
	 *
	 * @throws OrmException on failure.
	 */
	public function rollBack()
	{
		try
		{
			$this->gateway->rollBack();
		}
		catch (\RuntimeException $e)
		{
			throw new OrmException("Unable to start transaction.\n" . $e->getMessage(), 0, $e);
		}
	}
}
