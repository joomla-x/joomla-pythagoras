<?php
/**
 * Part of the Joomla! Content Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Plugin\Content\UpperCase\Listener;
use Joomla\Renderer\Event\RenderContentTypeEvent;
use Joomla\Content\Type\Compound;
use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Headline;

class UpperCaseListener
{

	public function toUpperCase (RenderContentTypeEvent $event)
	{
		$contentType = $event->getArgument('content');
		$this->toUpper($contentType);
	}

	private function toUpper (ContentTypeInterface $content)
	{
		if ($content instanceof Compound)
		{
			foreach ($content->items as $item)
			{
				$this->toUpper($item);
			}
		}
		else if ($content instanceof Paragraph)
		{
			$content->text = strtoupper($content->text);
		}
		else if ($content instanceof Headline)
		{
			$content->text = strtoupper($content->text);
		}
	}
}