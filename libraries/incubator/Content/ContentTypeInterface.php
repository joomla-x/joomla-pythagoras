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
 * @since    1.0
 */
interface ContentTypeInterface
{
	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 */
	public function accept(ContentTypeVisitorInterface $visitor);
}
