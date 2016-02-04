<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\QueryHandlerBase;

final class SimpleQueryHandler extends QueryHandlerBase
{
	public function handle(SimpleQuery $query)
	{
		return 'X' . $query->getTest() . 'Y';
	}
}
