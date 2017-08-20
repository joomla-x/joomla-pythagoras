<?php

namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\QueryHandler;

final class SimpleQueryHandler extends QueryHandler
{
    public function handle(SimpleQuery $query)
    {
        return 'X' . $query->getTest() . 'Y';
    }
}
