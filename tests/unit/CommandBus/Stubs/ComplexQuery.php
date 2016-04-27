<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;

use Joomla\CommandBus\Query;

final class ComplexQuery extends Query
{
	protected $arg1 = null;
	protected $arg2 = null;

	public function __construct($arg1 = null, $arg2 = null)
	{
		parent::__construct();

		$this->validate($arg1, $arg2);

		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
	}

	private function validate($arg1, $arg2)
	{
		if (is_null($arg1)) {
			throw new \RuntimeException('Argument 1 cannot be null');
		}
	}
}
