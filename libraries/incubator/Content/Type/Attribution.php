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
 * Attribution ContentType
 *
 * @package  Joomla/Content
 * @since    1.0
 *
 * @property string $label
 * @property string $name
 */
class Attribution extends AbstractContentType
{
	/**
	 * Attribution constructor.
	 *
	 * @param   string  $label  The text before the author's name
	 * @param   string  $name   The author's name
	 */
	public function __construct($label, $name)
	{
		$this->label = $label;
		$this->name  = $name;
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
		return $renderer->visitAttribution($this);
	}
}
