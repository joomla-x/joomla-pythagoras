<?php
/**
 * Part of the Joomla PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder;

use Interop\Container\ContainerInterface;
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
	private $vars;
	private $stream;
	private $container;

	/**
	 * DisplayPageCommand constructor.
	 *
	 * @param   integer            $id     The page ID
	 * @param   StreamInterface    $stream The output stream
	 * @param   ContainerInterface $container
	 */
	public function __construct($id, $vars, $stream, $container)
	{
		$this->id        = $id;
		$this->vars      = $vars;
		$this->stream    = $stream;
		$this->container = $container;

		parent::__construct();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getVars()
	{
		return $this->vars;
	}

	/**
	 * @return StreamInterface
	 */
	public function getStream()
	{
		return $this->stream;
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}
}
