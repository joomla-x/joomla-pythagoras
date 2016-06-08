<?php
/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Session;

use Aura\Session\SessionFactory;

/**
 * Class JoomlaSession
 *
 * @package Joomla/Session
 *
 * @since 1.0
 */
class Session implements SessionInterface
{

	/** @var \Aura\Session\Session */
	private $session = null;

	public function __construct($cookieParams)
	{
		$this->session = (new SessionFactory())->newInstance($cookieParams);
	}

	public function get($key)
	{
		return $this->session->getSegment('Joomla')->get($key);
	}

	public function set($key, $value)
	{
		return $this->session->getSegment('Joomla')->set($key, $value);
	}
}
