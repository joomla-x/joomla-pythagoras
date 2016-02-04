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
 * @package  joomla/renderer
 * @since    1.0
 */
class Factory
{
    protected $mediaTypeMap = [
        // CLI formats
        'text/plain' => 'PlainRenderer',
        'text/ansi' => 'AnsiRenderer',

        // REST formats
        'application/xml' => 'XmlRenderer',
        'application/json' => 'JsonRenderer',

        // Web/Office formats
        'text/html' => 'HtmlRenderer',
        'application/pdf' => 'PdfRenderer',

        // The DocBook format seems not to be registered. @link http://wiki.docbook.org/DocBookMimeType
        'application/docbook+xml' => 'DocbookRenderer',
        'application/vnd.oasis.docbook+xml' => 'DocbookRenderer',
        'application/x-docbook' => 'DocbookRenderer',
    ];

    /**
     * @param   string $acceptHeader
     *
     * @return  mixed
     */
    public function create($acceptHeader = '*/*')
    {
        $header = new AcceptHeader($acceptHeader);

        $match = $header->getBestMatch(array_keys($this->mediaTypeMap));

        if (!isset($match['token'])) {
            throw(new NotFoundException("No matching renderer found for\n\t$acceptHeader"));
        }

        $classname = __NAMESPACE__ . '\\' . $this->mediaTypeMap[$match['token']];

        return new $classname($match);
    }
}
