<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content;

/**
 * ContentType Interface
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 */
interface CompoundTypeInterface extends ContentTypeInterface
{
	/**
	 * Add a content element as a child
	 *
	 * @param   ContentTypeInterface $content The content element
	 *
	 * @return  void
	 */
	public function add(ContentTypeInterface $content);
}
