<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Psr\Container\ContainerInterface;
use Joomla\Content\ContentTypeInterface;
use Joomla\Renderer\Exception\NotFoundException;

/**
 * Class Renderer
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
abstract class Renderer implements RendererInterface
{
    /** @var string The MIME type */
    protected $mediatype = 'application/binary';

    /** @var  array Accepted range, ie., MIME type ('token') and quality ('q') */
    protected $options;

    /** @var string The output buffer */
    protected $output = '';

    /** @var  ContainerInterface */
    protected $container;

    /** @var callable[] Content type handlers */
    private $handlers = [];

    /** @var int Internal cursor */
    private $pointer = 0;

    /**
     * Renderer constructor.
     *
     * @param   array               $options    Accepted range, ie., MIME type ('token') and quality ('q')
     * @param   ContainerInterface  $container  The container
     */
    public function __construct(array $options, ContainerInterface $container)
    {
        $this->options   = $options;
        $this->container = $container;

        $this->registerFallback();
    }

    /**
     * Register a content type
     *
     * @param   string                $type    The content type
     * @param   callable|array|string $handler The handler for that type
     *
     * @return  void
     */
    public function registerContentType($type, $handler)
    {
        if (is_string($handler)) {
            $handler = function (ContentTypeInterface $contentItem) use ($handler) {
                return call_user_func([$contentItem, $handler]);
            };
        } elseif (is_array($handler)) {
            $handler = function (ContentTypeInterface $contentItem) use ($handler) {
                return call_user_func($handler, $contentItem);
            };
        }

        $this->handlers[strtolower($type)] = $handler;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * @param   string $method    Method name; must start with 'visit'
     * @param   array  $arguments Method arguments
     *
     * @return  void
     */
    public function __call($method, $arguments)
    {
        if (preg_match('~^visit(.+)~', $method, $match)) {
            $type = strtolower($match[1]);

            if (!isset($this->handlers[$type])) {
                $type = 'default';
            }

            if (isset($this->handlers[$type])) {
                $handler = $this->handlers[$type];
                $this->output .= $handler($arguments[0]);
            } else {
                throw new NotFoundException("Unknown content type {$match[1]}, no default\n");
            }
        }
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
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->output;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return  void
     */
    public function close()
    {
        $this->output  = '';
        $this->pointer = 0;
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return  resource|null  Underlying PHP stream, if any
     */
    public function detach()
    {
        return null;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return  integer|null  Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        return strlen($this->output);
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return  int  Position of the file pointer
     * @throws  \RuntimeException on error.
     */
    public function tell()
    {
        return $this->pointer;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return  boolean
     */
    public function eof()
    {
        return $this->pointer >= strlen($this->output);
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
        $this->seek(0);
    }

    /**
     * /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @param   int $offset    Stream offset
     * @param   int $whence    Specifies how the cursor position will be calculated
     *                         based on the seek offset. Valid values are identical to the built-in
     *                         PHP $whence values for `fseek()`.
     *                         SEEK_SET: Set position equal to offset bytes
     *                         SEEK_CUR: Set position to current location plus offset
     *                         SEEK_END: Set position to end-of-stream plus offset.
     *
     * @return  void
     * @throws  \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($whence == SEEK_SET) {
            $this->pointer = $offset;
        } elseif ($whence == SEEK_CUR) {
            $this->pointer += $offset;
        } elseif ($whence == SEEK_END) {
            $this->pointer = strlen($this->output) + $offset;
        } else {
            throw new \RuntimeException('Unknown mode. Expected one of SEEK_SET, SEEK_CUR, or SEEK_END');
        }
    }

    /**
     * Write data to the stream.
     *
     * @param   ContentTypeInterface|string $content The string that is to be written.
     *
     * @return  integer  Returns the number of bytes written to the stream.
     * @throws  \RuntimeException on failure.
     */
    public function write($content)
    {
        if ($content instanceof ContentTypeInterface) {
            $len = $content->accept($this);
        } else {
            $this->output .= $content;
            $len = strlen($content);
        }

        return $len;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return  boolean
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * Read data from the stream.
     *
     * @param   int $length   Read up to $length bytes from the object and return
     *                        them. Fewer than $length bytes may be returned if underlying stream
     *                        call returns fewer bytes.
     *
     * @return  string  Returns the data read from the stream, or an empty string
     *                  if no bytes are available.
     * @throws  \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        $currentPos = $this->pointer;
        $this->seek($length, SEEK_CUR);

        return substr($this->output, $currentPos, $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return   string
     * @throws   \RuntimeException if unable to read or an error occurs while
     *           reading.
     */
    public function getContents()
    {
        $currentPos = $this->pointer;
        $this->seek(1, SEEK_END);

        return substr($this->output, $currentPos);
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
     * @return  array|mixed|null  Returns an associative array if no key is
     *                            provided. Returns a specific key value if a key is provided and the
     *                            value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $metaData = $this->collectMetadata();

        if (is_null($key)) {
            return $metaData;
        }

        if (isset($metaData[$key])) {
            return $metaData[$key];
        }

        return null;
    }

    /**
     * @return  array
     */
    protected function collectMetadata()
    {
        return [
            'wrapper_data' => [
                'renderer' => get_class($this),
            ],
            'wrapper_type' => 'RFC2397',
            'stream_type'  => 'RFC2397',
            'mode'         => $this->isWritable() ? 'r+' : 'r',
            'unread_bytes' => strlen($this->output) - $this->pointer,
            'seekable'     => $this->isSeekable(),
            'uri'          => 'data://' . $this->mediatype . ',' . urlencode($this->output),
            'mediatype'    => $this->mediatype,
            'base64'       => false,
        ];
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return  boolean
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return  boolean
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * Define a fallback for non-registered content types.
     * The fallback will just ignore the content type.
     *
     * @return  void
     */
    private function registerFallback()
    {
        $this->registerContentType(
            'default',
            function () {
                return '';
            }
        );
    }
}
