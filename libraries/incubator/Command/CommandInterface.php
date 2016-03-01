<?php
/**
 * Part of the Joomla Framework Command Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Command;

use Joomla\Renderer\RendererInterface;

/**
 * Interface CommandInterface
 *
 * @package  Joomla/command
 *
 * @since    1.0
 */
interface CommandInterface
{
	/**
	 * @param array $input Attributes derived from the request.
	 * @param RendererInterface $output The output stream
	 *
	 * @return void
	 */
	public function execute($input, $output);
}
