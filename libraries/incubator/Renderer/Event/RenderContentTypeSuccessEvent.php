<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Event;

use Joomla\Event\Event;
use Joomla\Renderer\RendererInterface;

/**
 * Class RenderContentTypeSuccessEvent
 *
 * @package Joomla\Renderer
 *
 * @since   __DEPLOY_VERSION__
 */
class RenderContentTypeSuccessEvent extends Event
{
	/**
	 * RenderContentTypeSuccessEvent constructor.
	 *
	 * @param   string            $type     The name of the content type
	 * @param   RendererInterface $renderer The renderer to which the content is added
	 */
	public function __construct($type, $renderer)
	{
		parent::__construct(
			'onAfter' . $type,
			[
				'type'     => $type,
				'renderer' => clone($renderer)
			]
		);
	}
}
