<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\QueryBase;

final class SimpleQuery extends QueryBase
{
	public function __construct($test = null)
	{
		$this->test = $test;

		parent::__construct();
	}
}
