<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Mocks;

use Joomla\ORM\UnitOfWork\UnitOfWork as BaseUnitOfWork;

/**
 * Mocks the unit of work for testing
 */
class UnitOfWork extends BaseUnitOfWork
{
	/**
	 * @inheritDoc
	 */
	public function checkForUpdates()
	{
		parent::checkForUpdates();
	}

	/**
	 * @inheritDoc
	 */
	public function getScheduledEntityDeletions()
	{
		return parent::getScheduledEntityDeletions();
	}

	/**
	 * @inheritDoc
	 */
	public function getScheduledEntityInsertions()
	{
		return parent::getScheduledEntityInsertions();
	}

	/**
	 * @inheritDoc
	 */
	public function getScheduledEntityUpdates()
	{
		return parent::getScheduledEntityUpdates();
	}
}
