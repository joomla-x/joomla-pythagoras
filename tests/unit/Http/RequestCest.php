<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

use UnitTester;
use Zend\Diactoros\ServerRequestFactory;

class RequestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function GeneratedRequestObjectIsPsr7Compatible(UnitTester $tester)
    {
        $tester->assertTrue(ServerRequestFactory::fromGlobals() instanceof \Psr\Http\Message\RequestInterface, 'Request does not follow PSR-7');
    }
}
