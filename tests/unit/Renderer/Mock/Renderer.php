<?php

namespace Joomla\Tests\Unit\Renderer\Mock;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Article;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\DefaultMenu;
use Joomla\Content\Type\Dump;
use Joomla\Content\Type\Image;
use Joomla\Content\Type\Link;
use Joomla\Content\Type\OnePager;
use Joomla\Content\Type\OnePagerSection;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
use Joomla\Content\Type\Span;
use Joomla\Content\Type\Tabs;
use Joomla\Content\Type\Teaser;
use Joomla\Content\Type\Tree;

class Renderer extends \Joomla\Renderer\Renderer
{
	public function visitContent(ContentType $content)
	{
		$str = "standard: " . $content->getContents() . "\n";
		$this->output .= $str;

		return strlen($str);
	}

	/**
	 * Render a headline.
	 *
	 * @param   \Joomla\Content\Type\Headline $headline The headline
	 *
	 * @return  void
	 */
	public function visitHeadline(\Joomla\Content\Type\Headline $headline)
	{
	}

	/**
	 * Render a compound (block) element
	 *
	 * @param   \Joomla\Content\Type\Compound $compound The compound
	 *
	 * @return  void
	 */
	public function visitCompound(\Joomla\Content\Type\Compound $compound)
	{
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   \Joomla\Content\Type\Attribution $attribution The attribution
	 *
	 * @return  void
	 */
	public function visitAttribution(\Joomla\Content\Type\Attribution $attribution)
	{
	}

	/**
	 * Render a paragraph
	 *
	 * @param   \Joomla\Content\Type\Paragraph $paragraph The paragraph
	 *
	 * @return  void
	 */
	public function visitParagraph(\Joomla\Content\Type\Paragraph $paragraph)
	{
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
	}

	/**
	 * Dump an item
	 *
	 * @param   Dump $dump The dump
	 *
	 * @return  void
	 */
	public function visitDump(Dump $dump)
	{
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
	}
}
