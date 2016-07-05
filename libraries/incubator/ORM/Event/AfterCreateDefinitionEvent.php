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
 * Class AfterCreateDefinitionEvent
 *
 * @package Joomla\ORM
 *
 * @since  1.0
 */
class AfterCreateDefinitionEvent extends Event
{
	/**
	 * AfterCreateDefinitionEvent constructor.
	 *
	 * @param   string         $entityName  The name of the entity
	 * @param   Entity         $definition  The definition
	 * @param   EntityBuilder  $builder     The builder
	 */
	public function __construct($entityName, Entity $definition, EntityBuilder $builder)
	{
		parent::__construct(
			'onAfterCreateDefinition',
			[
				'entityClass' => $entityName,
				'definition' => $definition,
				'builder'    => $builder
			]
		);
	}
}
