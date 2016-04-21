<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeVisitorInterface;

/**
 * Renderer Interface
 *
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
interface RendererInterface extends ContentTypeVisitorInterface
{
	/**
	 * @param   string                $type    The content type
	 * @param   callable|array|string $handler The handler for that type
	 *
	 * @return  void
	 */
	public function registerContentType($type, $handler);
}
