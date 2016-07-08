<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Mocks;

/**
 * Mocks a user object for use in testing
 */
class User
{
	/** @var int The user Id */
	private $id = -1;

	/** @var int The Id of an imaginary aggregate root (eg parent) of this user */
	private $aggregateRootId = -1;

	/** @var int The Id of a second imaginary aggregate root of this user */
	private $secondAggregateRootId = -1;

	/** @var string The username */
	private $username = "";

	/**
	 * @param int    $id       The user Id
	 * @param string $username The username
	 */
	public function __construct($id, $username)
	{
		$this->id       = $id;
		$this->username = $username;
	}

	/**
	 * @return int
	 */
	public function getAggregateRootId()
	{
		return $this->aggregateRootId;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getSecondAggregateRootId()
	{
		return $this->secondAggregateRootId;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param int $aggregateRootId
	 */
	public function setAggregateRootId($aggregateRootId)
	{
		$this->aggregateRootId = $aggregateRootId;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param int $secondAggregateRootId
	 */
	public function setSecondAggregateRootId($secondAggregateRootId)
	{
		$this->secondAggregateRootId = $secondAggregateRootId;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}
}
