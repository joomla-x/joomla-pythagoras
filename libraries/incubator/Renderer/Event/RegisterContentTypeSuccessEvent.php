<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Event;

use Joomla\Event\Event;

class RegisterContentTypeSuccessEvent extends Event
{
	/**
	 * RegisterContentTypeSuccessEvent constructor.
	 *
	 * @param string   $type
	 * @param callable $handler
	 */
	public function __construct($type, $handler)
	{
		parent::__construct(
			'onAfterRegisterContentType',
			[
				'type'    => $type,
				'handler' => $handler
			]
		);
	}
}
