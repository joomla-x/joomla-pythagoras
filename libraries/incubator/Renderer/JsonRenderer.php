<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

/**
 * Class JsonRenderer
 *
 * @package  Joomla/renderer
 * @since    1.0
 */
class JsonRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'application/json';

	protected $data = [];

	public function visitHeadline(Headline $headline)
	{
		$this->data[] = ['headline' => ['text' => $headline->text, 'level' => $headline->level]];

		return 0;
	}

	public function visitCompound(Compound $compound)
	{
		$stash = $this->data;
		$this->data = [];

		foreach ($compound->items as $item)
		{
			$item->accept($this);
		}

		$stash[] = [$compound->type => $this->data];
		$this->data = $stash;

		return 0;
	}

	public function visitAttribution(Attribution $attribution)
	{
		$this->data[] = ['attribution' => ['label' => $attribution->label, 'name' => $attribution->name]];

		return 0;
	}

	public function visitParagraph(Paragraph $paragraph)
	{
		$this->data[] = ['paragraph' => ['text' => $paragraph->text, 'variant' => $paragraph->variant]];

		return 0;
	}
}
