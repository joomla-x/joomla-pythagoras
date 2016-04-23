<?php

namespace Joomla\Tests\Unit\Renderer\Mock;

class Renderer extends \Joomla\Renderer\Renderer
{
	public function visitContent(ContentType $content)
	{
		$str = "standard: " . $content->getContents() . "\n";
		$this->output .= $str;

		return strlen($str);
	}
	
	/**
	 * Render a headline.
	 *
	 * @param   \Joomla\Content\Type\Headline $headline The headline
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitHeadline(\Joomla\Content\Type\Headline $headline)
	{
		return 0;
	}

	/**
	 * Render a compound (block) element
	 *
	 * @param   \Joomla\Content\Type\Compound $compound The compound
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitCompound(\Joomla\Content\Type\Compound $compound)
	{
		return 0;
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   \Joomla\Content\Type\Attribution $attribution The attribution
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitAttribution(\Joomla\Content\Type\Attribution $attribution)
	{
		return 0;
	}

	/**
	 * Render a paragraph
	 *
	 * @param   \Joomla\Content\Type\Paragraph $paragraph The paragraph
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitParagraph(\Joomla\Content\Type\Paragraph $paragraph)
	{
		return 0;
	}
}
