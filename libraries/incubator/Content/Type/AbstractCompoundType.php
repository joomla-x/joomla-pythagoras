<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\CompoundTypeInterface;
use Joomla\Content\ContentTypeInterface;

/**
 * Abstract ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 */
abstract class AbstractCompoundType extends AbstractContentType implements CompoundTypeInterface
{
	/** @var  ContentTypeInterface[] Content elements */
	public $elements = [];

	/**
	 * Constructor.
	 *
	 * @param   string                 $title    The title
	 * @param   ContentTypeInterface[] $elements Content elements
	 */
	public function __construct($title, $elements = [])
	{
		$this->title = $title;

		foreach ($elements as $element)
		{
			$this->add($element);
		}
	}

	/**
	 * Add a content element as a child
	 *
	 * @param   ContentTypeInterface $content  The content element
	 * @param   string               $title    The title
	 * @param   string               $link     The link
	 *
	 * @return  void
	 */
	public function add(ContentTypeInterface $content, $title = null, $link = null)
	{
		$this->elements[] = (object) [
			'content' => $content,
			'title'   => $title ?: $content->getTitle(),
			'link'    => $link
		];
	}
}
