<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

/**
 * Class DocbookRenderer
 *
 * @package  Joomla/renderer
 * @since    1.0
 */
class DocbookRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'application/docbook+xml';

	public function visitHeadline(Headline $headline)
	{
		// TODO: Implement visitHeadline() method.
	}

	public function visitCompound(Compound $compound)
	{
		// TODO: Implement visitCompound() method.
	}

	public function visitAttribution(Attribution $attribution)
	{
		// TODO: Implement visitAttribution() method.
	}

	public function visitParagraph(Paragraph $paragraph)
	{
		// TODO: Implement visitParagraph() method.
	}
}
