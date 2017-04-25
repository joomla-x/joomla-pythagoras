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

/**
 * Class JsonRenderer
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
class JsonRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'application/json';

	/** @var array The collected data */
	protected $data = [];

	/** @var int The current output buffer length */
	protected $len = 0;

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
		throw new \LogicException($method . ' is not implemented.');
	}

	/**
	 * Get the content from the buffer
	 *
	 * @return string
	 */
	public function __toString()
	{
		return json_encode($this->data, JSON_PRETTY_PRINT);
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
		$this->data[] = ['attribution' => ['label' => $attribution->label, 'name' => $attribution->name]];
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
		$stash      = $this->data;
		$this->data = [];

		foreach ($compound->elements as $item)
		{
			$item->content->accept($this);
		}

		$stash[]    = [$compound->type => $this->data];
		$this->data = $stash;
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
		$this->data[] = ['headline' => ['text' => $headline->text, 'level' => $headline->level]];
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
		$this->data[] = ['paragraph' => ['text' => $paragraph->text, 'variant' => $paragraph->variant]];
	}
}
