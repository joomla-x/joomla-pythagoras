<?php
/**
 * Part of the Joomla! Category Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Extension\Category\Command;

use Joomla\Cms\Service\ContentTypeQuery;
use Joomla\Content\Type\Paragraph;
use Joomla\Service\QueryHandler;

/**
 * Category Command Handler
 *
 * @package Joomla/Extension/Category
 *
 * @since   1.0
 */
class AddCategoryContentTypesHandler extends QueryHandler
{
	/**
	 * @param   ContentTypeQuery  $query  The query
	 *
	 * @return  mixed
	 */
	public function handle(ContentTypeQuery $query)
	{
		if (!isset($query->entity->category))
		{
			return $query->elements;
		}

		$category = $query->entity->category;

		$elements                   = $query->elements;
		$elements['category_title'] = new Paragraph($category->title);

		return $elements;
	}
}
