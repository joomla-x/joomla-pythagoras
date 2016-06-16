<?php
/**
 * Part of the Joomla! Cms Service Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Service;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\Extension\Article\Command\DisplayCommand;
use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Repository\RepositoryQuery;
use Joomla\Service\CommandHandler;

/**
 * AbstractDisplayCommandHandler
 *
 * @package  Joomla/Service
 *
 * @since    1.0
 */
class BasicDisplayCommandHandler extends CommandHandler
{
	/**
	 * Execute the DisplayCommand.
	 *
	 * @param   DisplayCommand $command The command to execute.
	 *
	 * @return  void
	 */
	public function handle(BasicDisplayCommand $command)
	{
		$repository = $this->getCommandBus()->handle(new RepositoryQuery($command->entityName));
		$entity     = $repository->findById($command->id);

		if (!$entity instanceof EntityInterface)
		{
			return;
		}

		$compound = new Compound(
			$command->entityName,
			$this->getElements($entity)
		);

		$compound->accept($command->renderer);
	}

	/**
	 * Returns an array of ContentTypeInterface's. Subclasses can override it to
	 * add component specific elements.
	 *
	 * @param   EntityInterface $entity The entity
	 *
	 * @return  \Joomla\Content\ContentTypeInterface[]
	 */
	protected function getElements(EntityInterface $entity)
	{
		$elements = [];

		if ($entity->has('title'))
		{
			$elements['title'] = new Headline($entity->title, 1);
		}

		if ($entity->has('author'))
		{
			$elements['author'] = new Attribution('Written by', $entity->author);
		}

		if ($entity->has('teaser'))
		{
			$elements['teaser'] = new Paragraph($entity->teaser, Paragraph::EMPHASISED);
		}

		if ($entity->has('body'))
		{
			$elements['body'] = new Paragraph($entity->body);
		}

		$elementsData = $this->getCommandBus()->handle(new ContentTypeQuery($entity, $elements));

		foreach ($elementsData as $data)
		{
			$elements = array_merge($elements, $data);
		}

		return $elements;
	}
}
