<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeInterface;
use Joomla\Renderer\RendererInterface;

/**
 * Headline ContentType
 *
 * @package  Joomla/Content
 * @since    1.0
 *
 * @property string $type
 * @property ContentTypeInterface[] $items
 */
class Compound extends AbstractContentType
{
	/**
	 * Compound constructor.
	 *
	 * @param   string                  $type   The type represented by this class. In HTML, it is rendered as enclosing tag.
	 * @param   ContentTypeInterface[]  $items  The items enclosed by this tag
	 */
	public function __construct($type, $items)
	{
		$this->type = $type;
		$this->items = array_filter($items);
	}

	/**
	 * Add content items to the compound.
	 *
	 * @param   ContentTypeInterface  $content  The content to add
	 *
	 * @return  void
	 */
	public function add(ContentTypeInterface $content)
	{
		$this->items[] = $content;
	}

	/**
	 * Render the output
	 *
	 * @param   RendererInterface  $renderer  The Renderer
	 *
	 * @return  integer  Length of rendered content
	 */
	public function accept(RendererInterface $renderer)
	{
		return $renderer->visitCompound($this);
	}
}
