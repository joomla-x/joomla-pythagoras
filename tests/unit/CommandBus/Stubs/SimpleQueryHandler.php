<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;

use Joomla\CommandBus\QueryHandler;

final class SimpleQueryHandler extends QueryHandler
{
	public function handle(SimpleQuery $query)
	{
		return 'X' . $query->getTest() . 'Y';
	}
}
