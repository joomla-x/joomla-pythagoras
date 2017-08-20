<?php

namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\Query;

/**
 * Class SimpleQuery
 *
 * @package Joomla\Tests\Unit\Service\Stubs
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
