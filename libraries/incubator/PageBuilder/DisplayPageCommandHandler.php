<?php
/**
 * Part of the Joomla PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder;

use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\PageBuilder\Entity\Page;
use Joomla\Service\CommandHandler;

/**
 * Class DisplayPageCommandHandler
 *
 * @package Joomla\PageBuilder
 */
class DisplayPageCommandHandler extends CommandHandler
{
	/**
	 * @param DisplayPageCommand $command
	 */
	public function handle(DisplayPageCommand $command)
	{
		$id        = $command->getId();
		$stream    = $command->getStream();
		$container = $command->getContainer();

		/** @var RepositoryInterface $repository */
		$repository = $container->get('Repository')->forEntity(Page::class);

		$page = $repository->getById($id);
		echo "<pre>";
		print_r($page);
		echo "</pre>";
	}
}
