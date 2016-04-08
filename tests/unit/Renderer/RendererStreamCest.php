<?php
/**
 * Part of the Joomla Framework Renderer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Renderer;

use Joomla\Tests\Unit\Renderer\Mock\ContentType;
use Joomla\Tests\Unit\Renderer\Mock\Renderer;
use UnitTester;

class RendererStreamCest
{
	public function _before(UnitTester $I)
	{
		require_once __DIR__ . '/Mock/Content.php';
	}

	public function _after(UnitTester $I)
	{
	}

	/**
	 * @testdox __toString() returns the serialised content
	 */
	public function ToString(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$I->assertEquals("standard: This is the content.\n", (string)$renderer);
	}

	/**
	 * @testdox close() resets the buffer
	 */
	public function Close(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);
		$renderer->close();

		$I->assertEquals('', (string)$renderer);
		$I->assertEquals(0, $renderer->tell());
	}

	/**
	 * @testdox detach() always returns null
	 */
	public function Detach(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);

		$I->assertEquals(null, $renderer->detach());
	}

	/**
	 * @testdox getSize() returns the length of the buffer
	 */
	public function getSize(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$I->assertEquals(strlen("standard: This is the content.\n"), $renderer->getSize());
	}

	/**
	 * @testdox Stream is seekable by default
	 */
	public function IsSeekable(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);

		$I->assertTrue($renderer->isSeekable(), 'Expected isSeekable() to be true');
	}

	/**
	 * @testdox Stream is readable by default
	 */
	public function IsReadable(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);

		$I->assertTrue($renderer->isReadable(), 'Expected isReadable() to be true');
	}

	/**
	 * @testdox Stream is writable by default
	 */
	public function IsWritable(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);

		$I->assertTrue($renderer->isWritable(), 'Expected isWritable() to be true');
	}

	/**
	 * @testdox Stream pointer is reset on rewind()
	 */
	public function StreamPointerIsReset(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$renderer->rewind();
		$I->assertEquals(0, $renderer->tell());
	}

	/**
	 * @testdox Stream pointer is forwarded on read
	 */
	public function StreamPointerForwarding(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$renderer->rewind();
		$I->assertEquals('sta', $renderer->read(3));
		$I->assertEquals(3, $renderer->tell());
	}

	/**
	 * @testdox Stream pointer can be set relatively
	 */
	public function StreamPointerCanBeSetRelatively(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$I->assertEquals('sta', $renderer->read(3));
		$renderer->seek(2, SEEK_CUR);
		$I->assertEquals(5, $renderer->tell());
		$I->assertEquals('ard', $renderer->read(3));
	}

	/**
	 * @testdox Stream pointer can be set absolutely
	 */
	public function StreamPointerCanBeSetAbsolutely(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$renderer->seek(2, SEEK_SET);
		$I->assertEquals('and', $renderer->read(3));
		$I->assertEquals(5, $renderer->tell());
	}

	/**
	 * @testdox Stream pointer can be set relative to the stream's end
	 */
	public function StreamPointerCanBeSetRelativeToTheStreamsEnd(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$renderer->seek(-3, SEEK_END);
		$I->assertEquals(28, $renderer->tell());
		$I->assertEquals("t.\n", $renderer->read(3));
	}

	/**
	 * @testdox EOF is set, when the end of the stream is reached
	 */
	public function EofIsSetWhenTheEndOfTheStreamIsReached(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$renderer->seek(0, SEEK_END);

		$I->assertTrue($renderer->eof());
	}

	/**
	 * @testdox Exception is thrown on illegal whence value
	 */
	public function ExceptionIsThrownOnIllegalWhenceValue(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		try
		{
			$renderer->seek(0, 5);
			$I->fail('Expected RuntimeException was not thrown');
		} catch (\Exception $e)
		{
			$I->assertTrue($e instanceof \RuntimeException);
		}
	}

	/**
	 * @testdox write() appends string to the output
	 */
	public function WriteAppendsToTheOutput(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');

		$content->accept($renderer);

		$renderer->write("More of this.");

		$I->assertEquals("standard: This is the content.\nMore of this.", (string)$renderer);
	}

	/**
	 * @testdox write() accepts content type elements
	 */
	public function WriteAcceptsContentTypeElements(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$content  = new ContentType('This is the content.');
		$more     = new ContentType('More of this.');

		$content->accept($renderer);

		$renderer->write($more);

		$I->assertEquals("standard: This is the content.\nstandard: More of this.\n", (string)$renderer);
	}

	/**
	 * @testdox The meta data has the defined structure
	 */
	public function Metadata(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$meta = $renderer->getMetadata();
		
		$I->assertEquals([
				'wrapper_data',
				'wrapper_type',
				'stream_type',
				'mode',
				'unread_bytes',
				'seekable',
				'uri',
				'mediatype',
				'base64'
			],
			array_keys($meta)
		);
	}

	/**
	 * @testdox Stream type is RFC2397
	 */
	public function MetadataStreamType(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$meta     = $renderer->getMetadata('stream_type');

		$I->assertEquals('RFC2397', $meta);
	}

	/**
	 * @testdox Unknown meta data is set to null
	 */
	public function MetadataUnknown(UnitTester $I)
	{
		$renderer = new Renderer(['token' => '*/*']);
		$meta     = $renderer->getMetadata('foo');

		$I->assertEquals(null, $meta);
	}
}
