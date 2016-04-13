<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Event;

use Joomla\Content\ContentTypeInterface;
use Joomla\Event\Event;

class RenderContentTypeEvent extends Event
{
	/**
	 * RenderContentTypeEvent constructor.
	 *
	 * @param string               $type
	 * @param ContentTypeInterface $content
	 */
	public function __construct($type, $content)
	{
		parent::__construct(
			'onBeforeRenderContentType',
			[
				'type'    => $type,
				'content' => $content
			]
		);
	}
}
