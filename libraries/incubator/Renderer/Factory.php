<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Http\Header\AcceptHeader;
use Joomla\Renderer\Exception\NotFoundException;

/**
 * Class Factory
 *
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
class Factory
{
	/** @var array Mapping of MIME types to matching renderers */
	protected $mediaTypeMap = [
		// CLI formats
		'text/plain'                        => 'Joomla\Renderer\PlainRenderer',
		'text/ansi'                         => 'Joomla\Renderer\AnsiRenderer',

		// REST formats
		'application/xml'                   => 'Joomla\Renderer\XmlRenderer',
		'application/json'                  => 'Joomla\Renderer\JsonRenderer',

		// Web/Office formats
		'text/html'                         => 'Joomla\Renderer\HtmlRenderer',
		'application/pdf'                   => 'Joomla\Renderer\PdfRenderer',

		// The DocBook format seems not to be registered. @link http://wiki.docbook.org/DocBookMimeType
		'application/docbook+xml'           => 'Joomla\Renderer\DocbookRenderer',
		'application/vnd.oasis.docbook+xml' => 'Joomla\Renderer\DocbookRenderer',
		'application/x-docbook'             => 'Joomla\Renderer\DocbookRenderer',
	];

	/**
	 * @param   string  $acceptHeader  The 'Accept' header
	 *
	 * @return  mixed
	 */
	public function create($acceptHeader = '*/*')
	{
		$header = new AcceptHeader($acceptHeader);

		$match = $header->getBestMatch(array_keys($this->mediaTypeMap));

		if (!isset($match['token']))
		{
			throw(new NotFoundException("No matching renderer found for\n\t$acceptHeader"));
		}

		$classname = $this->mediaTypeMap[$match['token']];

		return new $classname($match);
	}
}
