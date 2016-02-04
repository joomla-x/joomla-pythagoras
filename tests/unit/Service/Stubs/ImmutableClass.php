<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\Immutable;

final class ImmutableClass extends Immutable
{
	public function __construct($test = null)
	{
		$this->test = $test;

		parent::__construct();
	}
}
