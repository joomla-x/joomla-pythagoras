<?php
/**
 * Part of the Joomla PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder;

use Joomla\Service\Command;
use Psr\Http\Message\StreamInterface;

/**
 * Class DisplayPageCommand
 *
 * @package Joomla\PageBuilder
 */
class DisplayPageCommand extends Command
{
	private $id;
	private $stream;

	/**
	 * DisplayPageCommand constructor.
	 *
	 * @param   integer          $id      The page ID
	 * @param   StreamInterface  $stream  The output stream
	 */
	public function __construct($id, $stream)
	{
		$this->id     = $id;
		$this->stream = $stream;

		parent::__construct();
	}
}
