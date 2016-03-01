<?php

namespace Joomla\Component\Content\Command;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

class Display extends AbstractCommand
{
	public function execute($input, $output)
	{
		$article = $this->repository->findById($input['command']['id']);

		$compound = new Compound('article', [
			new Headline($article->title, 1),
			new Attribution('Written by', $article->author),
			new Paragraph($article->teaser, Paragraph::EMPHASISED),
			new Paragraph($article->body),
		]);

		foreach ($article->children as $child)
		{
			$compound->add(new Compound('section', [
				new Headline($child->title, 2),
				$child->author != $article->author ? new Attribution('Contribution from', $child->author) : null,
				new Paragraph($child->body),
			]));
		}

		$compound->accept($output);
	}
}
