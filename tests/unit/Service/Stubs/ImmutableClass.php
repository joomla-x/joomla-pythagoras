<?php

namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\Immutable;

/**
 * Class ImmutableClass
 *
 * @package Joomla\Tests\Unit\Service\Stubs
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
