<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Renderer\RendererInterface;

/**
 * Paragraph ContentType
 *
 * @package  Joomla/Content
 * @since    1.0
 *
 * @property string $text
 * @property integer $variant One of the class constants
 */
class Paragraph extends AbstractContentType
{
	const PLAIN = 0;
	const EMPHASISED = 1;

	/**
	 * Paragraph constructor.
	 *
	 * @param   string   $text     The copy of the paragraph
	 * @param   integer  $variant  Emphasis variant, see class constants
	 */
	public function __construct($text, $variant = self::PLAIN)
	{
		$this->text    = $text;
		$this->variant = $variant;
	}

	/**
	 * Render the output
	 *
	 * @param   RendererInterface  $renderer  The Renderer
	 *
	 * @return  integer  Length of rendered content
	 */
	public function accept(RendererInterface $renderer)
	{
		return $renderer->visitParagraph($this);
	}
}
