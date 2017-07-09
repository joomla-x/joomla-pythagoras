<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorTrait;
use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Article;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\DefaultMenu;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Image;
use Joomla\Content\Type\Link;
use Joomla\Content\Type\OnePager;
use Joomla\Content\Type\OnePagerSection;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
use Joomla\Content\Type\Span;
use Joomla\Content\Type\Tabs;
use Joomla\Content\Type\Teaser;
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
 * @since    __DEPLOY_VERSION__
 */
class EventDecorator implements RendererInterface
{
	/** @var RendererInterface */
	private $renderer;

	/** @var DispatcherInterface */
	private $dispatcher;

	use ContentTypeVisitorTrait;

	/**
	 * Common handler for different ContentTypes.
	 *
	 * @param string               $method  The name of the originally called method
	 * @param ContentTypeInterface $content The content
	 *
	 * @return mixed
	 */
	public function visit($method, $content)
	{
		$this->delegate($method, [$content]);
	}

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
	 * Get the content from the buffer
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->renderer;
	}

	/**
	 * Get the (inner) class of this renderer.
	 *
	 * @return string
	 */
	public function getClass()
	{
		return $this->renderer->getClass();
	}

	/**
	 * Get the media (MIME) type for this renderer.
	 *
	 * @return string
	 */
	public function getMediaType()
	{
		return $this->renderer->getMediaType();
	}

	/**
	 * Register a handler for a content type.
	 *
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
	 * Write data to the output.
	 *
	 * @param   ContentTypeInterface|string $content The string that is to be written.
	 *
	 * @return  void
	 */
	public function write($content)
	{
		$this->renderer->write($content);
	}

	/**
	 * Delegate all methods.
	 *
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
				call_user_func_array([$this->renderer, $method], $arguments);
				$this->dispatcher->dispatch(new RenderContentTypeSuccessEvent($type, $this->renderer));

				return null;
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
}
