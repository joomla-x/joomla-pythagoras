<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

/**
 * Class PdfRenderer
 *
 * @package  Joomla/renderer
 * @since    1.0
 */
class PdfRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'application/pdf';
}