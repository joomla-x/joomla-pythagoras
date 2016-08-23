<?php
/**
 * Part of the Joomla! User Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Extension\User\Command;
use Joomla\Service\QueryHandler;
use Joomla\Cms\Service\ContentTypeQuery;
use Joomla\Content\Type\Attribution;
use Joomla\Session\SessionAwareTrait;

/**
 * User Command Handler
 *
 * @package Joomla/Extension/User
 *
 * @since 1.0
 */
class AddAuthorContentTypesHandler extends QueryHandler
{
	use SessionAwareTrait;

	public function handle (ContentTypeQuery $query)
	{
		if (!isset($query->entity->author))
		{
			return $query->elements;
		}

		$author = $query->entity->author;

		$elements = $query->elements;
		$elements['author'] = new Attribution('Contribution from ', $author->name);
		return $elements;
	}
}
