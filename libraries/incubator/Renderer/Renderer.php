<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Interop\Container\ContainerInterface;
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

	/**
	 * Renderer constructor.
	 *
	 * @param   array              $options   Accepted range, ie., MIME type ('token') and quality ('q')
	 * @param   ContainerInterface $container The container
	 */
	public function __construct(array $options, ContainerInterface $container)
	{
		$this->options   = $options;
		$this->container = $container;

		$this->registerFallback();
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
			function ()
			{
				return '';
			}
		);
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
		if (is_string($handler))
		{
			$handler = function (ContentTypeInterface $contentItem) use ($handler)
			{
				return call_user_func([$contentItem, $handler]);
			};
		}
		elseif (is_array($handler))
		{
			$handler = function (ContentTypeInterface $contentItem) use ($handler)
			{
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
	 * @return string
	 */
	public function getMediaType()
	{
		return $this->mediatype;
	}

	/**
	 * Write data to the output.
	 *
	 * @param   ContentTypeInterface|string $content The string that is to be written.
	 *
	 * @return  void
	 */
	public function write($content)
	{
		if ($content instanceof ContentTypeInterface)
		{
			$content->accept($this);
		}
		else
		{
			$this->output .= $content;
		}
	}

	/**
	 * Get the content from the buffer
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->output;
	}

	/**
	 * @param   string $method    Method name; must start with 'visit'
	 * @param   array  $arguments Method arguments
	 *
	 * @return  void
	 */
	public function __call($method, $arguments)
	{
		if (preg_match('~^visit(.+)~', $method, $match))
		{
			$type = strtolower($match[1]);

			if (!isset($this->handlers[$type]))
			{
				$type = 'default';
			}

			if (isset($this->handlers[$type]))
			{
				$handler      = $this->handlers[$type];
				$this->output .= $handler($arguments[0]);
			}
			else
			{
				throw new NotFoundException("Unknown content type {$match[1]}, no default\n");
			}
		}
	}
}
