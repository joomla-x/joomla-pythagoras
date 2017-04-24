<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Article;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\DefaultMenu;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Image;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
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
	 * Get the content from the buffer
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->renderer;
	}

	/**
	 * Render a headline.
	 *
	 * @param   Headline $headline The headline
	 *
	 * @return  void
	 */
	public function visitHeadline(Headline $headline)
	{
		$this->delegate('visitHeadline', [$headline]);
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

	/**
	 * Render a compound (block) element
	 *
	 * @param   Compound $compound The compound
	 *
	 * @return  void
	 */
	public function visitCompound(Compound $compound)
	{
		$this->delegate('visitCompound', [$compound]);
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   Attribution $attribution The attribution
	 *
	 * @return  void
	 */
	public function visitAttribution(Attribution $attribution)
	{
		$this->delegate('visitAttribution', [$attribution]);
	}

	/**
	 * Render a paragraph
	 *
	 * @param   Paragraph $paragraph The paragraph
	 *
	 * @return  void
	 */
	public function visitParagraph(Paragraph $paragraph)
	{
		$this->delegate('visitParagraph', [$paragraph]);
	}

	/**
	 * Render an image
	 *
	 * @param   Image $image The image
	 *
	 * @return  void
	 */
	public function visitImage(Image $image)
	{
		$this->delegate('visitImage', [$image]);
	}

	/**
	 * Render an slider
	 *
	 * @param   Slider $slider The slider
	 *
	 * @return  void
	 */
	public function visitSlider(Slider $slider)
	{
		$this->delegate('visitSlider', [$slider]);
	}

	/**
	 * Render an accordion
	 *
	 * @param   Accordion $accordion The accordion
	 *
	 * @return  void
	 */
	public function visitAccordion(Accordion $accordion)
	{
		$this->delegate('visitAccordion', [$accordion]);
	}

	/**
	 * Render a tree
	 *
	 * @param   Tree $tree The tree
	 *
	 * @return  void
	 */
	public function visitTree(Tree $tree)
	{
		$this->delegate('visitTree', [$tree]);
	}

	/**
	 * Render tabs
	 *
	 * @param   Tabs $tabs The tabs
	 *
	 * @return  void
	 */
	public function visitTabs(Tabs $tabs)
	{
		$this->delegate('visitTabs', [$tabs]);
	}

	/**
	 * Dump an item
	 *
	 * @param   ContentTypeInterface $dump The dump
	 *
	 * @return  void
	 */
	public function visitDump(ContentTypeInterface $dump)
	{
		$this->delegate('visitDump', [$dump]);
	}

	/**
	 * Render rows
	 *
	 * @param   Rows $rows The rows
	 *
	 * @return  void
	 */
	public function visitRows(Rows $rows)
	{
		$this->delegate('visitRows', [$rows]);
	}

	/**
	 * Render columns
	 *
	 * @param   Columns $columns The columns
	 *
	 * @return  void
	 */
	public function visitColumns(Columns $columns)
	{
		$this->delegate('visitColumns', [$columns]);
	}

	/**
	 * Render an article
	 *
	 * @param   Article $article The article
	 *
	 * @return  void
	 */
	public function visitArticle(Article $article)
	{
		$this->delegate('visitArticle', [$article]);
	}

	/**
	 * Render a teaser
	 *
	 * @param   Teaser $teaser The teaser
	 *
	 * @return  void
	 */
	public function visitTeaser(Teaser $teaser)
	{
		$this->delegate('visitTeaser', [$teaser]);
	}

	/**
	 * Render a defaultMenu
	 *
	 * @param   DefaultMenu $defaultMenu The defaultMenu
	 *
	 * @return  void
	 */
	public function visitDefaultMenu(DefaultMenu $defaultMenu)
	{
		$this->delegate('visitDefaultMenu', [$defaultMenu]);
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
}
