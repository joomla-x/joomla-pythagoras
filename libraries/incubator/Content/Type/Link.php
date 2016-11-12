<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeVisitorInterface;

/**
 * Link ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property object $item
 */
class Link extends AbstractContentType
{
	/**
	 * @var string
	 */
	private $href;
	/**
	 * @var
	 */
	private $text;

	/**
	 * Link constructor.
	 *
	 */
	public function __construct($href, $text)
	{
		$this->href = $href;
		$this->text = $text;
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
		return $visitor->visitLink($this);
	}
}
