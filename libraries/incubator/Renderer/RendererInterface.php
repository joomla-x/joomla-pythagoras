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
use Psr\Http\Message\StreamInterface;

/**
 * Renderer Interface
 *
 * @package  Joomla/renderer
 * @since    1.0
 */
interface RendererInterface extends StreamInterface
{
	/**
	 * @param   string   $type     The content type
	 * @param   callable $handler  The handler for that type
	 *
	 * @return  void
	 */
	public function registerContentType($type, callable $handler);

	public function visitHeadline(Headline $headline);
	public function visitCompound(Compound $compound);
	public function visitAttribution(Attribution $attribution);
	public function visitParagraph(Paragraph $paragraph);
}
