<?php

namespace Joomla\Tests\Unit\Content\Mock;

use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

/**
 * Class Renderer
 *
 * @package Joomla\Tests\Unit\Content\Mock
 */
class Renderer extends \Joomla\Renderer\Renderer
{
	/** @var array The collected data */
	public $data = [];

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

		return 0;
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
		$stash      = $this->data;
		$this->data = [];

		foreach ($compound->items as $item)
		{
			$item->accept($this);
		}

		$stash[]    = [$compound->type => $this->data];
		$this->data = $stash;

		return 0;
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

		return 0;
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

		return 0;
	}
}
