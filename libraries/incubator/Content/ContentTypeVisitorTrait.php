<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content;

use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Article;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\DefaultMenu;
use Joomla\Content\Type\Dump;
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
 * Content Type Visitor Trait
 *
 * This trait redirects calls to `visit<ContentType>($content)` methods to visit('visit<ContentType>', $content).
 * This is very handy, if the implementations for the methods are very similar, since interfaces can not be
 * implemented using `__call()`.
 *
 * @package  Joomla\Content
 *
 * @since    __DEPLOY_VERSION__
 */
trait ContentTypeVisitorTrait
{
	/**
	 * Common handler for different ContentTypes.
	 *
	 * @param string               $method  The name of the originally called method
	 * @param ContentTypeInterface $content The content
	 *
	 * @return mixed
	 */
	abstract public function visit($method, $content);

	/**
	 * Render an accordion
	 *
	 * @param   Accordion $accordion The accordion
	 *
	 * @return  void
	 */
	public function visitAccordion(Accordion $accordion)
	{
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
	}

	/**
	 * Render dump
	 *
	 * @param   Dump $dump The dump
	 *
	 * @return  void
	 */
	public function visitDump(Dump $dump)
	{
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
	}

	/**
	 * Render a link
	 *
	 * @param   Link $link The link
	 *
	 * @return  void
	 */
	public function visitLink(Link $link)
	{
		$this->visit(__FUNCTION__, func_get_arg(0));
	}

	/**
	 * Render a one-pager
	 *
	 * @param   OnePager $onePager The one-pager
	 *
	 * @return  void
	 */
	public function visitOnePager(OnePager $onePager)
	{
		$this->visit(__FUNCTION__, func_get_arg(0));
	}

	/**
	 * Render a one-pager section
	 *
	 * @param   OnePagerSection $onePagerSection The one-pager section
	 *
	 * @return  void
	 */
	public function visitOnePagerSection(OnePagerSection $onePagerSection)
	{
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
	}

	/**
	 * Render an span
	 *
	 * @param   Span $span The span
	 *
	 * @return  void
	 */
	public function visitSpan(Span $span)
	{
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
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
		$this->visit(__FUNCTION__, func_get_arg(0));
	}
}
