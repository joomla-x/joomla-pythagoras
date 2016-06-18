<?php
/**
 * Part of the Joomla! Article Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Article\Command;

use Joomla\Cms\Service\BasicDisplayCommandHandler;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\ORM\Entity\EntityInterface;

/**
 * Display Command Handler
 *
 * @package  Joomla/Extension/Article
 *
 * @since    1.0
 */
class DisplayCommandHandler extends BasicDisplayCommandHandler
{
	/**
	 * Returns an array of ContentTypeInterface's. Subclasses can override it to
	 * add component specific elements.
	 *
	 * @param   EntityInterface $entity The entity
	 *
	 * @return  \Joomla\Content\ContentTypeInterface[]
	 */
	protected function getElements(EntityInterface $entity)
	{
		$elements = parent::getElements($entity);

		foreach ($entity->children as $child)
		{
			$elements[] = new Compound(
				'section',
				[
					new Headline($child->title, 2),
					new Attribution('Contribution from', $child->author),
					new Paragraph($child->body),
				]
			);
		}

		return $elements;
	}
}
