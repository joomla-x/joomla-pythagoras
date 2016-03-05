<?php
/**
 * Part of the Joomla! Content Component Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Component\Content\Command;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryQuery;
use Joomla\Service\CommandHandler;

/**
 * Display Command Handler
 *
 * @package  Joomla/Component/Content
 *
 * @since    1.0
 */
class DisplayCommandHandler extends CommandHandler
{
	/**
	 * Execute the DisplayCommand.
	 *
	 * @param   DisplayCommand  $command  The command to execute.
	 *
	 * @return  void
	 */
	public function handle(DisplayCommand $command)
	{
		$articleRepository = $this->getCommandBus()->handle(new RepositoryQuery($command->entityName));
		$article           = $articleRepository->findById($command->id);

		$compound = new Compound(
			'article',
			[
				new Headline($article->title, 1),
				new Attribution('Written by', $article->author),
				new Paragraph($article->teaser, Paragraph::EMPHASISED),
				new Paragraph($article->body),
			]
		);

		foreach ($article->children as $child)
		{
			$compound->add(
				new Compound(
					'section',
					[
						new Headline($child->title, 2),
						$child->author != $article->author ? new Attribution('Contribution from', $child->author) : null,
						new Paragraph($child->body),
					]
				)
			);
		}

		$compound->accept($command->renderer);
	}
}
