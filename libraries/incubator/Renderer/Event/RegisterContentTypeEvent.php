<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Event;

use Joomla\Event\Event;

/**
 * Class RegisterContentTypeEvent
 *
 * @package Joomla\Renderer
 *
 * @since  1.0
 */
class RegisterContentTypeEvent extends Event
{
	/**
	 * RegisterContentTypeEvent constructor.
	 *
	 * @param   string    $type     The name of the content type
	 * @param   callable  $handler  The content type handler
	 */
	public function __construct($type, $handler)
	{
		parent::__construct(
			'onBeforeRegisterContentType',
			[
				'type'    => $type,
				'handler' => $handler
			]
		);
	}
}
