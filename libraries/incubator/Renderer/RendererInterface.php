<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;

/**
 * Renderer Interface
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
interface RendererInterface extends ContentTypeVisitorInterface
{
	/**
	 * Register a handler for a content type.
	 *
	 * @param   string                $type    The content type
	 * @param   callable|array|string $handler The handler for that type
	 *
	 * @return  void
	 */
	public function registerContentType($type, $handler);

	/**
	 * Get the (inner) class of this renderer.
	 *
	 * @return string
	 */
	public function getClass();

	/**
	 * Get the media (MIME) type for this renderer.
	 *
	 * @return string
	 */
	public function getMediaType();

	/**
	 * Write data to the output.
	 *
	 * @param   ContentTypeInterface|string $content The string that is to be written.
	 *
	 * @return  void
	 */
	public function write($content);

	/**
	 * Get the content from the buffer
	 *
	 * @return string
	 */
	public function __toString();
}
