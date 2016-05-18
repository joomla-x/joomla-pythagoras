<?php
/**
 * Part of the Joomla! Article Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Article\Command;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\Cms\Service\BasicDisplayCommandHandler;

/**
 * Display Command Handler
 *
 * @package  Joomla/Extension/Article
 *
 * @since    1.0
 */
class DisplayCommandHandler extends BasicDisplayCommandHandler
{
	protected function getElements(EntityInterface $entity)
	{
		$elements = parent::getElements($entity);

		foreach ($entity->children as $child)
		{
			$elements[] = new Compound(
				'section',
				[
					new Headline($child->title, 2),
					$child->author != $article->author ? new Attribution('Contribution from', $child->author) : null,
					new Paragraph($child->body),
				]
			);
		}

		return $elements;
	}
}
