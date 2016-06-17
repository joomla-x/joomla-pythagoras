<?php
/**
 * Part of the Joomla Cms Service Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Service;

use Joomla\ORM\Entity\EntityInterface;
use Joomla\Service\Query;

/**
 * Content Type Query
 *
 * @package  Joomla/Cms/Service
 *
 * @since    1.0
 */
class ContentTypeQuery extends Query
{
	/**
	 * ContentTypeQuery constructor.
	 *
	 * @param   EntityInterface $entity   The entity to be rendered
	 * @param   array           $elements The elements created so far
	 */
	public function __construct(EntityInterface $entity, $elements)
	{
		$this->entity   = $entity;
		$this->elements = $elements;

		parent::__construct();
	}
}
