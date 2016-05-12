<?php
/**
 * Part of the Joomla! Content Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\UpperCase\Listener;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\Renderer\Event\RenderContentTypeEvent;

/**
 * Class UpperCaseListener
 *
 * @package Joomla\Extension\Content
 *
 * @since   1.0
 */
class UpperCaseListener
{
	/**
	 * Event handler
	 *
	 * @param   RenderContentTypeEvent $event The event
	 *
	 * @return  void
	 */
	public function toUpperCase(RenderContentTypeEvent $event)
	{
		$contentType = $event->getArgument('content');
		$this->toUpper($contentType);
	}

	/**
	 * Convert strings to uppercase
	 *
	 * @param   ContentTypeInterface $content The content
	 *
	 * @return  void
	 */
	private function toUpper(ContentTypeInterface $content)
	{
		if ($content instanceof Compound)
		{
			foreach ($content->items as $item)
			{
				$this->toUpper($item);
			}
		}
		elseif ($content instanceof Paragraph)
		{
			$content->text = strtoupper($content->text);
		}
		elseif ($content instanceof Headline)
		{
			$content->text = strtoupper($content->text);
		}
	}
}
