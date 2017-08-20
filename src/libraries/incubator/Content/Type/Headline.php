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
 * Headline ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property string  $text
 * @property integer $level
 */
class Headline extends AbstractContentType
{
	/**
	 * Headline constructor.
	 *
	 * @param   string  $text  The copy of the headline
	 * @param   integer $level The Level of the headline
	 */
	public function __construct($text, $level = 1)
	{
		parent::__construct($text, 'headline-' . spl_object_hash($this), new \stdClass);

		$this->text  = $text;
		$this->level = $level;
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
		return $visitor->visitHeadline($this);
	}
}
