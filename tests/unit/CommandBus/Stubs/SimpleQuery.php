<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;

use Joomla\CommandBus\Query;

/**
 * Class SimpleQuery
 *
 * @package Joomla\Tests\Unit\CommandBus\Stubs
 *
 * @method string getName()
 * @method string getTest()
 */
final class SimpleQuery extends Query
{
	public function __construct($test = null)
	{
		$this->test = $test;

		parent::__construct();
	}
}
