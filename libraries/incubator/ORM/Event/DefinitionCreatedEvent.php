<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Event;

use Joomla\Event\Event;
use Joomla\ORM\Definition\Parser\Entity;
use Joomla\ORM\Entity\EntityBuilder;

/**
 * Class DefinitionCreatedEvent
 *
 * @package Joomla\ORM
 *
 * @since   __DEPLOY_VERSION__
 */
class DefinitionCreatedEvent extends Event
{
	/**
	 * DefinitionCreatedEvent constructor.
	 *
	 * @param   string        $entityClass The class of the entity
	 * @param   Entity        $definition  The definition
	 * @param   EntityBuilder $builder     The builder
	 */
	public function __construct($entityClass, Entity $definition, EntityBuilder $builder)
	{
		parent::__construct(
			'onDefinitionCreated',
			[
				'entityClass' => $entityClass,
				'definition'  => $definition,
				'builder'     => $builder
			]
		);
	}

	/**
	 * @return   string
	 */
	public function getEntityClass()
	{
		return $this->getArgument('className');
	}

	/**
	 * @return   Entity
	 */
	public function getDefinition()
	{
		return $this->getArgument('definition');
	}

	/**
	 * @return   EntityBuilder
	 */
	public function getEntityBuilder()
	{
		return $this->getArgument('builder');
	}
}
