<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeQuery;
use Joomla\Content\ContentTypeVisitorInterface;
use Joomla\ORM\Entity\EntityInterface;

/**
 * Headline ContentType
 *
 * @package  Joomla/Content
 * @since    1.0
 *
 * @property string $title
 * @property string $author
 * @property string $teaser
 */
class Teaser extends AbstractEntityContentType
{
	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 *
	 * @return  mixed
	 *
	 * @todo    The body property will need some special handling (parsing),
	 *          since it might contain other elements (sub-headings, image links, ...)
	 */
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		$elements = [
			'title'  => new Headline($this->title, 1),
			'author' => new Attribution('Written by', $this->author),
			'teaser' => new Paragraph($this->teaser, Paragraph::EMPHASISED),
		];

		$elements = $this->commandBus->handle(new ContentTypeQuery($this->entity, $elements));

		$compound = new Compound(
			'article',
			$elements
		);

		return $visitor->visitCompound($compound);
	}
}
