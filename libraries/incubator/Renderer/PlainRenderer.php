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
 * @package  Joomla/renderer
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

	public function visitHeadline(Headline $headline)
	{
		return $this->write($headline->text . "\n" . str_repeat('=', strlen($headline->text)) . "\n\n");
	}

	public function visitCompound(Compound $compound)
	{
		$len = 0;
		foreach ($compound->items as $item)
		{
			$len += $item->accept($this);
		}
		return $len;
	}

	public function visitAttribution(Attribution $attribution)
	{
		return $this->write($attribution->label . ' ' . $attribution->text . "\n\n");
	}

	public function visitParagraph(Paragraph $paragraph)
	{
		return $this->write($paragraph->text . "\n\n");
	}
}
