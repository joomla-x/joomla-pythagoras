<?php
/**
 * Part of the Joomla! User Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Extension\User\Command;

use Joomla\Cms\Service\BasicDisplayCommandHandler;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

/**
 * Display Command Handler
 *
 * @package Joomla/Extension/User
 *
 * @since 1.0
 */
class DisplayCommandHandler extends BasicDisplayCommandHandler
{

	protected function getElements(EntityInterface $entity)
	{
		$elements = parent::getElements($entity);

		$elements['title'] = new Headline('Details of ' . $entity->name, 1);
		$elements['body'] = new Paragraph('Username: ' . $entity->username);

		return $elements;
	}
}
