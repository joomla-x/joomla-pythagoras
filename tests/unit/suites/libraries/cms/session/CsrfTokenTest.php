<?php
/**
 * @package        Joomla.UnitTest
 * @subpackage     Session
 *
 * @copyright      Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Session\Tests;

use Joomla\Cms\Session\CsrfToken;

/**
 * Test class for CsrfToken.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Session
 * @since       4.0
 */
class CsrfTokenTest extends \TestCase
{
	/** @var  \JSession */
	private $session;

	public function setUp()
	{
		$this->session = new \JSession();
		\TestReflection::setValue($this->session, '_state', 'active');
	}

	public function tearDown()
	{
		$this->session = null;
	}

	public function testGetReturnsSameTokenOnSubsequentCalls()
	{
		$token = new CsrfToken($this->session);
		$this->assertEquals($token->get(), $token->get());
	}
}
