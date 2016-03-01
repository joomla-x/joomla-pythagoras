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
 * Headline ContentType
 *
 * @package  Joomla/Content
 * @since    1.0
 *
 * @property string $text
 * @property integer $level
 */
class Headline extends AbstractContentType
{
	protected $text;
	protected $level = 1;

	public function __construct($text, $level = 1)
	{
		$this->text = $text;
		$this->level = $level;
	}

	public function accept(RendererInterface $renderer)
	{
		return $renderer->visitHeadline($this);
	}
}
