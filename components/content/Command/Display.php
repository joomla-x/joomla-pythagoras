<?php

namespace Joomla\Component\Content\Command;

class Display extends AbstractCommand
{
	public function execute($input, $output)
	{
		$article = $this->repository->findById($input['command']['id']);

		$output->write('<h1>' . $article->title . '</h1>');
		$output->write('<p><small>Written by ' . $article->author . '</small></p>');
		$output->write('<p><em>' . $article->teaser . '</em></p>');
		$output->write('<p>' . $article->body . '</p>');

		foreach ($article->children as $child)
		{
			$output->write('<h2>' . $child->title . '</h2>');
			if ($child->author != $article->author) {
				$output->write('<p><small>Contribution from ' . $child->author . '</small></p>');
			}
			$output->write('<p>' . $child->body . '</p>');
		}
	}
}
