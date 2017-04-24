<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;

/**
 * Columns ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 */
class Columns extends Compound
{
	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 *
	 * @return  void
	 */
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		$visitor->visitColumns($this);
	}
}
