<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeInterface;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\Service\CommandBus;

/**
 * Abstract ContentType based on an Entity
 *
 * @package  Joomla/Content
 * @since    1.0
 */
abstract class AbstractEntityContentType implements ContentTypeInterface
{
	/** @var EntityInterface The entity to be rendered */
	protected $entity;

	/** @var  CommandBus A command bus for queries */
	protected $commandBus;

	/** @var array 'expected property' => 'actual property' */
	private $mapping = [];

	/**
	 * Article constructor.
	 *
	 * @param   EntityInterface $entity     The entity to be rendered
	 * @param   CommandBus      $commandBus A command bus for queries
	 * @param   array           $mapping    A map 'expected property' => 'actual property'
	 */
	public function __construct(EntityInterface $entity, CommandBus $commandBus, $mapping = [])
	{
		$this->entity     = $entity;
		$this->commandBus = $commandBus;
		$this->mapping    = $mapping;
	}

	/**
	 * Magic getter.
	 *
	 * @param   string $var Name of the property
	 *
	 * @return  mixed
	 */
	public function __get($var)
	{
		if (key_exists($var, $this->mapping))
		{
			$var = $this->mapping[$var];
		}

		if ($this->entity->has($var))
		{
			return $this->entity->$var;
		}

		throw new \UnexpectedValueException("Unknown property $var");
	}
}
