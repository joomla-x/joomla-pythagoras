<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content;

use Joomla\Renderer\RendererInterface;

/**
 * ContentType Interface
 *
 * @package  Joomla/Content
 * @since    1.0
 */
interface ContentTypeInterface
{
	/**
	 * Render the output
	 *
	 * @param   RendererInterface $renderer The Renderer
	 *
	 * @return  integer  Length of rendered content
	 */
	public function accept(RendererInterface $renderer);
}
