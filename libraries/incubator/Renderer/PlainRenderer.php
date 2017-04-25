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
 * Class PlainRenderer
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
class PlainRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'text/plain';

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
	 * Render an attribution to an author
	 *
	 * @param   Attribution $attribution The attribution
	 *
	 * @return  void
	 */
	public function visitAttribution(Attribution $attribution)
	{
		$this->write($attribution->label . ' ' . $attribution->text . "\n\n");
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
			echo $content;
		}
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
		$this->visitCompound($columns);
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
		foreach ($compound->elements as $item)
		{
			$item->content->accept($this);
		}
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
		$this->write(print_r($dump->item, true));
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
		$this->write($headline->text . "\n" . str_repeat('=', strlen($headline->text)) . "\n\n");
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
		$this->write("![{$image->alt}]({$image->url})");
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
		$this->write($paragraph->text . "\n\n");
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
		$this->visitCompound($rows);
	}
}
