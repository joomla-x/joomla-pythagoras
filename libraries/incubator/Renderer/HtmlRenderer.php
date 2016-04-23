<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;

/**
 * Class HtmlRenderer
 *
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
class HtmlRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'text/html';

	/** @var string  Layout directory */
	protected $layoutDirectory = 'html';

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

	/**
	 * Render a headline.
	 *
	 * @param   Headline $headline The headline
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitHeadline(Headline $headline)
	{
		return $this->applyLayout('headline.php', $headline);
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
		return $this->applyLayout('attribution.php', $attribution);
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
		return $this->applyLayout('paragraph.php', $paragraph);
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
		$len = 0;
		$len += $this->write("<{$compound->type}>\n");

		foreach ($compound->items as $item)
		{
			$len += $item->accept($this);
		}

		$len += $this->write("</{$compound->type}>\n");

		return $len;
	}

	/**
	 * Apply a layout
	 *
	 * @param   string                $filename  The filename of the layout file
	 * @param   ContentTypeInterface  $content   The content
	 *
	 * @return  integer
	 */
	private function applyLayout($filename, $content)
	{
		ob_start();
		include JPATH_ROOT . '/layouts/' . $this->layoutDirectory . '/' . $filename;

		return $this->write(ob_get_clean());
	}
}
