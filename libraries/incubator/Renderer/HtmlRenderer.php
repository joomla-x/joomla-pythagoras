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
 * Class HtmlRenderer
 *
 * @package  Joomla/renderer
 * @since    1.0
 */
class HtmlRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'text/html';

	/** @var  ScriptStrategyInterface */
	private $clientScript;

	/**
	 * @param   ScriptStrategyInterface  $strategy  The scripting startegy (library) to use
	 *
	 * @return  void
	 */
	public function setScriptStrategy(ScriptStrategyInterface $strategy)
	{
		$this->clientScript = $strategy;
	}

	/**
	 * @return  array
	 */
	protected function collectMetadata()
	{
		$metaData                                  = parent::collectMetadata();
		$metaData['wrapper_data']['client_script'] = empty($this->clientScript) ? null : get_class($this->clientScript);

		return $metaData;
	}

	public function visitHeadline(Headline $headline)
	{
		return $this->write("<h{$headline->level}>{$headline->text}</h{$headline->level}>\n");
	}

	public function visitAttribution(Attribution $attribution)
	{
		return $this->write("<p><small>{$attribution->label} {$attribution->name}</small></p>\n");
	}

	public function visitParagraph(Paragraph $paragraph)
	{
		$text = $paragraph->text;

		switch ($paragraph->variant)
		{
			case Paragraph::EMPHASISED:
				$text = "<em>{$text}</em>";
				break;
		}

		return $this->write("<p>{$text}</p>\n");
	}

	public function visitCompound(Compound $compound)
	{
		$len = 0;
		$len += $this->write("<{$compound->type}>\n");
		foreach ($compound->items as $item)
		{
			$len += $item->accept($this);
		}
		$len += $this->write("</{$compound->type}>\n");

		return $len;
	}
}
