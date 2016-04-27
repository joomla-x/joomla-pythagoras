<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;

use Joomla\CommandBus\Command;

/**
 * Class SimpleCommand
 *
 * @package Joomla\Tests\Unit\CommandBus\Stubs
 *
 * @method string getName()
 * @method string getTest()
 */
final class SimpleCommand extends Command
{
	public function __construct($test = null)
	{
		$this->test = $test;

		parent::__construct();
	}
}
