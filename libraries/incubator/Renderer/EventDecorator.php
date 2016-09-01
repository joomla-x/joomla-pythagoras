<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Dump;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Image;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
use Joomla\Content\Type\Tabs;
use Joomla\Content\Type\Tree;
use Joomla\Event\DispatcherInterface;
use Joomla\Renderer\Event\RegisterContentTypeEvent;
use Joomla\Renderer\Event\RegisterContentTypeFailureEvent;
use Joomla\Renderer\Event\RegisterContentTypeSuccessEvent;
use Joomla\Renderer\Event\RenderContentTypeEvent;
use Joomla\Renderer\Event\RenderContentTypeFailureEvent;
use Joomla\Renderer\Event\RenderContentTypeSuccessEvent;

/**
 * Event Decorator for Renderer
 *
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
class EventDecorator implements RendererInterface
{
	/** @var RendererInterface */
	private $renderer;

	/** @var DispatcherInterface */
	private $dispatcher;

	/**
	 * Decorator constructor.
	 *
	 * @param   RendererInterface   $renderer   The renderer to be decorated
	 * @param   DispatcherInterface $dispatcher The dispather handling the events
	 */
	public function __construct(RendererInterface $renderer, DispatcherInterface $dispatcher)
	{
		$this->renderer   = $renderer;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @param   string                $type    The content type
	 * @param   callable|array|string $handler The handler for that type
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 */
	public function registerContentType($type, $handler)
	{
		$this->dispatcher->dispatch(new RegisterContentTypeEvent($type, $handler));

		try
		{
			$this->renderer->registerContentType($type, $handler);
			$this->dispatcher->dispatch(new RegisterContentTypeSuccessEvent($type, $handler));
		}
		catch (\Exception $exception)
		{
			$this->dispatcher->dispatch(new RegisterContentTypeFailureEvent($type, $exception));
			throw $exception;
		}
	}

	/**
	 * @param   string $method    Method name; must start with 'visit'
	 * @param   array  $arguments Method arguments
	 *
	 * @return  mixed
	 * @throws  \Exception
	 */
	public function __call($method, $arguments)
	{
		return $this->delegate($method, $arguments);
	}

	/**
	 * Reads all data from the stream into a string, from the beginning to end.
	 *
	 * This method MUST attempt to seek to the beginning of the stream before
	 * reading data and read the stream until the end is reached.
	 *
	 * Warning: This could attempt to load a large amount of data into memory.
	 *
	 * This method MUST NOT raise an exception in order to conform with PHP's
	 * string casting operations.
	 *
	 * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->renderer;
	}

	/**
	 * Closes the stream and any underlying resources.
	 *
	 * @return  void
	 */
	public function close()
	{
		$this->renderer->close();
	}

	/**
	 * Separates any underlying resources from the stream.
	 *
	 * After the stream has been detached, the stream is in an unusable state.
	 *
	 * @return  resource|null Underlying PHP stream, if any
	 */
	public function detach()
	{
		return $this->renderer->detach();
	}

	/**
	 * Get the size of the stream if known.
	 *
	 * @return  integer|null  Returns the size in bytes if known, or null if unknown.
	 */
	public function getSize()
	{
		return $this->renderer->getSize();
	}

	/**
	 * Returns the current position of the file read/write pointer
	 *
	 * @return  integer  Position of the file pointer
	 * @throws  \RuntimeException on error.
	 */
	public function tell()
	{
		return $this->renderer->tell();
	}

	/**
	 * Returns true if the stream is at the end of the stream.
	 *
	 * @return  boolean
	 */
	public function eof()
	{
		return $this->renderer->eof();
	}

	/**
	 * Returns whether or not the stream is seekable.
	 *
	 * @return  boolean
	 */
	public function isSeekable()
	{
		return $this->renderer->isSeekable();
	}

	/**
	 * Seek to a position in the stream.
	 *
	 * @link http://www.php.net/manual/en/function.fseek.php
	 *
	 * @param   int $offset Stream offset
	 * @param   int $whence Specifies how the cursor position will be calculated
	 *                      based on the seek offset. Valid values are identical to the built-in
	 *                      PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
	 *                      offset bytes SEEK_CUR: Set position to current location plus offset
	 *                      SEEK_END: Set position to end-of-stream plus offset.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException on failure.
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		$this->renderer->seek($offset, $whence);
	}

	/**
	 * Seek to the beginning of the stream.
	 *
	 * If the stream is not seekable, this method will raise an exception;
	 * otherwise, it will perform a seek(0).
	 *
	 * @see  seek()
	 * @link http://www.php.net/manual/en/function.fseek.php
	 *
	 * @return  void
	 * @throws  \RuntimeException on failure.
	 */
	public function rewind()
	{
		$this->renderer->rewind();
	}

	/**
	 * Returns whether or not the stream is writable.
	 *
	 * @return  boolean
	 */
	public function isWritable()
	{
		return $this->renderer->isWritable();
	}

	/**
	 * Write data to the stream.
	 *
	 * @param   string $string The string that is to be written.
	 *
	 * @return  integer  Returns the number of bytes written to the stream.
	 * @throws  \RuntimeException on failure.
	 */
	public function write($string)
	{
		return $this->renderer->write($string);
	}

	/**
	 * Returns whether or not the stream is readable.
	 *
	 * @return  boolean
	 */
	public function isReadable()
	{
		return $this->renderer->isReadable();
	}

	/**
	 * Read data from the stream.
	 *
	 * @param   int $length  Read up to $length bytes from the object and return
	 *                       them. Fewer than $length bytes may be returned if underlying stream
	 *                       call returns fewer bytes.
	 *
	 * @return  string  Returns the data read from the stream, or an empty string
	 *                  if no bytes are available.
	 *
	 * @throws \RuntimeException if an error occurs.
	 */
	public function read($length)
	{
		return $this->renderer->read($length);
	}

	/**
	 * Returns the remaining contents in a string
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException if unable to read or an error occurs while
	 *     reading.
	 */
	public function getContents()
	{
		return $this->renderer->getContents();
	}

	/**
	 * Get stream metadata as an associative array or retrieve a specific key.
	 *
	 * The keys returned are identical to the keys returned from PHP's
	 * stream_get_meta_data() function.
	 *
	 * @link http://php.net/manual/en/function.stream-get-meta-data.php
	 *
	 * @param   string $key Specific metadata to retrieve.
	 *
	 * @return  array|mixed|null Returns an associative array if no key is
	 *                           provided. Returns a specific key value if a key is provided and the
	 *                           value is found, or null if the key is not found.
	 */
	public function getMetadata($key = null)
	{
		return $this->renderer->getMetadata($key);
	}

	/**
	 * @param   string $method    The name of the method
	 * @param   array  $arguments The arguments
	 *
	 * @return  mixed
	 *
	 * @throws  \Exception
	 */
	private function delegate($method, $arguments)
	{
		if (preg_match('~^visit(.+)~', $method, $match))
		{
			$type = $match[1];
			$this->dispatcher->dispatch(new RenderContentTypeEvent($type, $arguments[0]));

			try
			{
				$result = call_user_func_array([$this->renderer, $method], $arguments);
				$this->dispatcher->dispatch(new RenderContentTypeSuccessEvent($type, $this->renderer));

				return $result;
			}
			catch (\Exception $exception)
			{
				$this->dispatcher->dispatch(new RenderContentTypeFailureEvent($type, $exception));
				throw $exception;
			}
		}
		else
		{
			return call_user_func_array([$this->renderer, $method], $arguments);
		}
	}

	/**
	 * Render a headline.
	 *
	 * @param   Headline $headline The headline
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitHeadline(Headline $headline)
	{
		return $this->delegate('visitHeadline', [$headline]);
	}

	/**
	 * Render a compound (block) element
	 *
	 * @param   Compound $compound The compound
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitCompound(Compound $compound)
	{
		return $this->delegate('visitCompound', [$compound]);
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   Attribution $attribution The attribution
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitAttribution(Attribution $attribution)
	{
		return $this->delegate('visitAttribution', [$attribution]);
	}

	/**
	 * Render a paragraph
	 *
	 * @param   Paragraph $paragraph The paragraph
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitParagraph(Paragraph $paragraph)
	{
		return $this->delegate('visitParagraph', [$paragraph]);
	}

	/**
	 * Render an image
	 *
	 * @param   Image $image The image
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitImage(Image $image)
	{
		return $this->delegate('visitImage', [$image]);
	}

	/**
	 * Render an slider
	 *
	 * @param   Slider $slider The slider
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitSlider(Slider $slider)
	{
		return $this->delegate('visitSlider', [$slider]);
	}

	/**
	 * Render an accordion
	 *
	 * @param   Accordion $accordion The accordion
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitAccordion(Accordion $accordion)
	{
		return $this->delegate('visitAccordion', [$accordion]);
	}

	/**
	 * Render a tree
	 *
	 * @param   Tree $tree The tree
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitTree(Tree $tree)
	{
		return $this->delegate('visitTree', [$tree]);
	}

	/**
	 * Render tabs
	 *
	 * @param   Tabs $tabs The tabs
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitTabs(Tabs $tabs)
	{
		return $this->delegate('visitTabs', [$tabs]);
	}

	/**
	 * Dump an item
	 *
	 * @param   Dump $dump The dump
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitDump(Dump $dump)
	{
		return $this->delegate('visitDump', [$dump]);
	}

	/**
	 * Render rows
	 *
	 * @param   Rows $rows The rows
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitRows(Rows $rows)
	{
		return $this->delegate('visitRows', [$rows]);
	}

	/**
	 * Render columns
	 *
	 * @param   Columns $columns The columns
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitColumns(Columns $columns)
	{
		return $this->delegate('visitColumns', [$columns]);
	}
}
