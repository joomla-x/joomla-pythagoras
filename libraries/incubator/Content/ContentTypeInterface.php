<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content;

use Interop\Container\ContainerInterface;
use Joomla\Renderer\RendererInterface;

/**
 * ContentType Interface
 *
 * @package  Joomla/Content
 *
 * @since    __DEPLOY_VERSION__
 */
interface ContentTypeInterface
{
	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 *
	 * @return  void
	 */
	public function accept(ContentTypeVisitorInterface $visitor);

	/**
	 * Gets the identifier for the content
	 *
	 * @return  string
	 */
	public function getId();

	/**
	 * Gets the title for the content
	 *
	 * @return  string
	 */
	public function getTitle();

	/**
	 * Gets the parameters for the content
	 *
	 * @return  \stdClass
	 */
	public function getParameters();

	/**
	 * Gets the parameters for the content
	 *
	 * @param   string  $key     The key
	 * @param   mixed   $default The default value
	 *
	 * @return  mixed
	 */
	public function getParameter($key, $default = null);
}
