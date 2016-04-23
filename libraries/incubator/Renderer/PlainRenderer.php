<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

/**
 * Class PlainRenderer
 *
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
class PlainRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'text/plain';

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
		if ($content instanceof ContentTypeInterface)
		{
			$len = $content->accept($this);
		}
		else
		{
			echo $content;
			$len = strlen($content);
		}

		return $len;
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
		return $this->write($headline->text . "\n" . str_repeat('=', strlen($headline->text)) . "\n\n");
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
		$len = 0;

		foreach ($compound->items as $item)
		{
			$len += $item->accept($this);
		}

		return $len;
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
		return $this->write($attribution->label . ' ' . $attribution->text . "\n\n");
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
		return $this->write($paragraph->text . "\n\n");
	}
}
