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

/**
 * Class RenderContentTypeEvent
 *
 * @package Joomla\Renderer
 *
 * @since  1.0
 */
class RenderContentTypeEvent extends Event
{
	/**
	 * RenderContentTypeEvent constructor.
	 *
	 * @param   string               $type     The name of the content type
	 * @param   ContentTypeInterface $content  The content element
	 */
	public function __construct($type, $content)
	{
		parent::__construct(
			'onBeforeRender' . $type,
			[
				'type'    => $type,
				'content' => $content
			]
		);
	}
}
