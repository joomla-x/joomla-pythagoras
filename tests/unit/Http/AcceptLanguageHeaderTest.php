<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

class AcceptLanguageHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function dataHeaders()
    {
        return [
            'simple' => [
                'requested' => 'Accept-Language: en-US',
                'provided' => ['de-DE', 'en-GB', 'en-US'],
                'expected' => ['token' => 'en-US']
            ],
            'wildcard' => [
                'requested' => 'Accept-Language: en',
                'provided' => ['de-DE', 'en-GB', 'en-US'],
                'expected' => ['token' => 'en-GB']
            ],
            'reallife-1' => [
                'requested' => 'Accept-Language: en-US,en;q=0.5',
                'provided' => ['de-DE', 'en-GB', 'en-US'],
                'expected' => ['token' => 'en-US']
            ],
        ];
    }

    /**
     * @dataProvider dataHeaders
     * @param $requested
     * @param $provided
     * @param $expected
     */
    public function testBestMatchIsNegotiated($requested, $provided, $expected)
    {
        $header = new \Joomla\Http\Header\AcceptLanguageHeader($requested);
        $accepted = $header->getBestMatch($provided);

        $this->assertArraySubset($expected, $accepted);
    }
}
