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
	protected $type;
	protected $items = [];

	/**
	 * Constructor.
	 *
	 * @param string                 $type
	 * @param ContentTypeInterface[] $items
	 */
	public function __construct($type, $items)
	{
		$this->type = $type;
		$this->items = array_filter($items);
	}

	public function add(ContentTypeInterface $content)
	{
		$this->items[] = $content;
	}

	public function accept(RendererInterface $renderer)
	{
		return $renderer->visitCompound($this);
	}
}
