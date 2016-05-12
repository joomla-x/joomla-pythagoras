<?php
/**
 * Part of the Joomla! Article Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Article\Command;

use Joomla\Content\Type\Article;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryQuery;
use Joomla\Renderer\Exception\NotFoundException;
use Joomla\Service\CommandHandler;

/**
 * Display Command Handler
 *
 * @package  Joomla/Extension/Article
 *
 * @since    1.0
 */
class DisplayCommandHandler extends CommandHandler
{
	/**
	 * Execute the DisplayCommand.
	 *
	 * @param   DisplayCommand $command The command to execute.
	 *
	 * @return  void
	 */
	public function handle(DisplayCommand $command)
	{
		try
		{
			$articleRepository = $this->getRepository($command->entityName);
			$article           = $articleRepository->findById($command->id);
		}
		catch (\Exception $e)
		{
			throw new NotFoundException($command->entityName . ' ' . $command->id . ' not found', 404);
		}

		$element = new Article($article, $this->getCommandBus());

		$element->accept($command->renderer);
	}

	/**
	 * @param   string $entityName The name of the entity
	 *
	 * @return  Repository
	 */
	private function getRepository($entityName)
	{
		return $this->getCommandBus()->handle(new RepositoryQuery($entityName));
	}
}
