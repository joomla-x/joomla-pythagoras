<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;

use Joomla\CommandBus\Immutable;

/**
 * Class ImmutableClass
 *
 * @package Joomla\Tests\Unit\CommandBus\Stubs
 *
 * @method string getTest()
 */
final class ImmutableClass extends Immutable
{
	public function __construct($test = null)
	{
		$this->test = $test;

		parent::__construct();
	}
}
