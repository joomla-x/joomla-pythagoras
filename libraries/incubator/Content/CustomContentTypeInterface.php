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
 * CustomContentTypeInterface Interface
 *
 * @package  Joomla/Content
 *
 * @since    __DEPLOY_VERSION__
 */
interface CustomContentTypeInterface extends ContentTypeInterface
{
	/**
	 * Register this content type to a renderer
	 *
	 * @param   RendererInterface $renderer The renderer
	 *
	 * @return  void
	 */
	public function register(RendererInterface $renderer);
}
