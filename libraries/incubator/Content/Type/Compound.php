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
 * Compound ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property string                 $type
 * @property ContentTypeInterface[] $elements
 */
class Compound extends AbstractCompoundType
{
	/**
	 * Compound constructor.
	 *
	 * @param   string $type The type represented by this class. In HTML, it is rendered as enclosing tag.
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 *
	 * @return  mixed
	 */
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		return $visitor->visitCompound($this);
	}
}
