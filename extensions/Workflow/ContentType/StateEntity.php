<?php
/**
 * Part of the Joomla Workflow Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Workflow\ContentType;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Paragraph;
use Joomla\Extension\Article\Entity\Article;

/**
 * Compound ContentType
 *
 * @package  Joomla/Workflow
 * @since    __DEPLOY_VERSION__
 *
 * @property string                 $type
 * @property ContentTypeInterface[] $elements
 */
class StateEntity extends Paragraph
{
	/**
	 * Compound constructor.
	 *
	 * @param   Article $item The entity.
	 */
	public function __construct(Article $item)
	{
		$this->entity = $item;

		$text = 'No State!';

		if (isset($item->stateEntities) && $rel = $item->stateEntities->getAll())
		{
			$text = $rel[0]->state->title;
		}

		parent::__construct('State: ' . $text);
	}
}
