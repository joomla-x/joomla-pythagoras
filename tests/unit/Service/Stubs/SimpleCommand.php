<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\CommandBase;

final class SimpleCommand extends CommandBase
{
	public function __construct($test = null)
	{
		$this->test = $test;

		parent::__construct();
	}
}
