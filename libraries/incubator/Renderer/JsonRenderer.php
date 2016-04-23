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
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
class JsonRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'application/json';

	/** @var array The collected data */
	protected $data = [];

	/** @var int The current output buffer length */
	protected $len = 0;

	/**
	 * Render a headline.
	 *
	 * @param   Headline $headline The headline
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitHeadline(Headline $headline)
	{
		$this->data[] = ['headline' => ['text' => $headline->text, 'level' => $headline->level]];

		return $this->updateContent();
	}

	/**
	 * Render a compound (block) element
	 *
	 * @param   Compound $compound The compound
	 *
	 * @return  integer Number of bytes written to the output
	 */
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

		return $this->updateContent();
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   Attribution $attribution The attribution
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitAttribution(Attribution $attribution)
	{
		$this->data[] = ['attribution' => ['label' => $attribution->label, 'name' => $attribution->name]];

		return $this->updateContent();
	}

	/**
	 * Render a paragraph
	 *
	 * @param   Paragraph $paragraph The paragraph
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitParagraph(Paragraph $paragraph)
	{
		$this->data[] = ['paragraph' => ['text' => $paragraph->text, 'variant' => $paragraph->variant]];

		return $this->updateContent();
	}

	/**
	 * Update the content
	 *
	 * @return integer
	 */
	private function updateContent()
	{
		$this->output = json_encode($this->data, JSON_PRETTY_PRINT);
		$total        = strlen($this->output);
		$len          = $total - $this->len;
		$this->len    = $total;

		return $len;
	}
}
