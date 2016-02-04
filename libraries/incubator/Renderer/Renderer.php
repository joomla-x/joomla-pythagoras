<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Psr\Http\Message\StreamInterface;

/**
 * Class Renderer
 *
 * @package  joomla/renderer
 * @since    1.0
 */
abstract class Renderer implements StreamInterface
{
    protected $mediatype = 'application/binary';

    protected $options;

    protected $output = '';

    private $handlers = [];

    private $pointer = 0;

    /**
     * Renderer constructor.
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @param   string $type
     * @param   callable $handler
     */
    public function registerContentType($type, callable $handler)
    {
        $this->handlers[strtolower($type)] = $handler;
    }

    /**
     * @param $method
     * @param $arguments
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
                echo "\nLogWarn: Unknown content type {$match[1]}, no default\n";
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->output = '';
        $this->pointer = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return strlen($this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->pointer >= strlen($this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        throw new \RuntimeException('Unable to write to the renderer directly. Use the visit() method.');
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $currentPos = $this->pointer;
        $this->seek($length, SEEK_CUR);

        return substr($this->output, $currentPos, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $currentPos = $this->pointer;
        $this->seek(1, SEEK_END);

        return substr($this->output, $currentPos);
    }

    /**
     * {@inheritdoc}
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
     * @return array
     */
    protected function collectMetadata()
    {
        return [
            'wrapper_data' => [
                'renderer' => get_class($this),
            ],
            'wrapper_type' => 'RFC2397',
            'stream_type' => 'RFC2397',
            'mode' => $this->isWritable() ? 'r+' : 'r',
            'unread_bytes' => strlen($this->output) - $this->pointer,
            'seekable' => $this->isSeekable(),
            'uri' => 'data://' . $this->mediatype . ',' . urlencode($this->output),
            'mediatype' => $this->mediatype,
            'base64' => false,
        ];
    }
}
