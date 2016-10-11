<?php
/**
 * Part of the Joomla Command Line Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Cli;

class BaseTest extends CliTestCase
{
	/**
	 * @testdox Invoking `joomla` without arguments displays the help message
	 */
	public function testWithoutParams()
	{
		$output = $this->runInShell('./joomla');
		$this->assertRegExp('/^Usage:/m', $output['stdout']);
		$this->assertRegExp('/Options:/m', $output['stdout']);
		$this->assertRegExp('/Available commands:/m', $output['stdout']);
	}
}
