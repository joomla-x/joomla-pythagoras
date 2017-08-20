<?php
/**
 * Part of the Joomla Framework PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder\ContentType;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;
use Joomla\Content\Type\AbstractContentType;

/**
 * Compound ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property string                 $type
 * @property ContentTypeInterface[] $elements
 */
class TemplateSelector extends AbstractContentType
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
		return $visitor->visitTemplateSelector($this);
	}
}
