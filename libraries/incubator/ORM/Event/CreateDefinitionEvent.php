<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Event;

use Joomla\Event\Event;
use Joomla\ORM\Entity\EntityBuilder;

/**
 * Class CreateDefinitionEvent
 *
 * @package Joomla\ORM
 *
 * @since   __DEPLOY_VERSION__
 */
class CreateDefinitionEvent extends Event
{
	/**
	 * DefinitionCreatedEvent constructor.
	 *
	 * @param   string        $entityName The name of the entity
	 * @param   EntityBuilder $builder    The builder
	 */
	public function __construct($entityName, EntityBuilder $builder)
	{
		parent::__construct(
			'onCreateDefinition',
			[
				'className' => $entityName,
				'builder'   => $builder
			]
		);
	}

	/**
	 * @return   string
	 */
	public function getEntityName()
	{
		return $this->getArgument('className');
	}

	/**
	 * @return   EntityBuilder
	 */
	public function getEntityBuilder()
	{
		return $this->getArgument('builder');
	}
}
