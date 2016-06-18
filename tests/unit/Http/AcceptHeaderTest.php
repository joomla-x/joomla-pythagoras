<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

class AcceptHeaderTest extends \PHPUnit_Framework_TestCase
{
	public function dataHeaders()
	{
		return [
			'simple'             => [
				'requested' => 'Accept: text/html',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html']
			],
			'wildcard'           => [
				'requested' => 'Accept: */*',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html']
			],
			'wildcard-secondary' => [
				'requested' => 'Accept: text/*',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html']
			],
			'wildcard-primary'   => [
				'requested' => 'Accept: */html',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html']
			],
			'wildcard-abbr'      => [
				'requested' => 'Accept: *',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html']
			],
			'version-info'       => [
				'requested' => 'Accept: text/html;extra=foo',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html', 'extra' => 'foo']
			],
			'version-bool'       => [
				'requested' => 'Accept: text/html;extra',
				'provided'  => ['text/html'],
				'expected'  => ['token' => 'text/html', 'extra' => true]
			],
			'reallife-1'         => [
				'requested' => 'Accept: text/*;q=0.3, text/html;q=0.7, text/html;level=1, text/html;level=2;q=0.4, */*;q=0.5',
				'provided'  => ['text/html', 'text/plain', 'application/xml', 'application/json'],
				'expected'  => ['token' => 'text/html']
			],
			'reallife-2'         => [
				'requested' => 'Accept: audio/*; q=0.2, audio/basic',
				'provided'  => ['text/html', 'text/plain', 'application/xml', 'application/json'],
				'expected'  => ['q' => 0.0]
			],
			'reallife-3'         => [
				'requested' => 'text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c',
				'provided'  => ['text/html', 'text/plain', 'application/xml', 'application/json'],
				'expected'  => ['token' => 'text/html']
			],
			'reallife-4'         => [
				'requested' => 'application/xml',
				'provided'  => ['text/html', 'text/plain', 'application/xml', 'application/json'],
				'expected'  => ['token' => 'application/xml']
			],
			'reallife-5'         => [
				'requested' => 'application/xml;q=0.8, application/json; q=0.9',
				'provided'  => ['text/html', 'text/plain', 'application/xml', 'application/json'],
				'expected'  => ['token' => 'application/json']
			],
			'reallife-6'         => [
				'requested' => 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'provided'  => ['text/html', 'text/plain', 'application/xml', 'application/json'],
				'expected'  => ['token' => 'text/html']
			],
		];
	}

	/**
	 * @dataProvider dataHeaders
	 *
	 * @param $requested
	 * @param $provided
	 * @param $expected
	 */
	public function testBestMatchIsNegotiated($requested, $provided, $expected)
	{
		$header   = new \Joomla\Http\Header\AcceptHeader($requested);
		$accepted = $header->getBestMatch($provided);

		$this->assertArraySubset($expected, $accepted);
	}
}
