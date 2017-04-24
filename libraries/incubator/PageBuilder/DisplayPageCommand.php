<?php
/**
 * Part of the Joomla PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder;

use Interop\Container\ContainerInterface;
use Joomla\Renderer\RendererInterface;
use Joomla\Service\Command;
use Psr\Http\Message\ServerRequestInterface;
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
	private $request;
	private $renderer;
	private $container;

	/**
	 * DisplayPageCommand constructor.
	 *
	 * @param   integer                $id       The page ID
	 * @param   array                  $vars     Routing variables
	 * @param   ServerRequestInterface $request  The request object
	 * @param   RendererInterface      $renderer The renderer
	 * @param   ContainerInterface     $container
	 */
	public function __construct($id, $vars, $request, $renderer, $container)
	{
		$this->id        = $id;
		$this->vars      = $vars;
		$this->request   = $request;
		$this->renderer  = $renderer;
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
	 * @return ServerRequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return StreamInterface
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}
}
