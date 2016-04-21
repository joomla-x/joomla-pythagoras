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
 * @since    1.0
 *
 * @property string $text
 * @property integer $level
 */
class Headline extends AbstractContentType
{
	/**
	 * Headline constructor.
	 *
	 * @param   string   $text   The copy of the headline
	 * @param   integer  $level  The Level of the headline
	 */
	public function __construct($text, $level = 1)
	{
		$this->text  = $text;
		$this->level = $level;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @see \Joomla\Content\ContentTypeInterface::accept()
	 */
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		return $visitor->visitHeadline($this);
	}
}
