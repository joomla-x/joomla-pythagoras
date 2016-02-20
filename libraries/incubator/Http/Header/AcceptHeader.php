<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Header;

/**
 * Class AcceptHeader
 *
 * @package  Joomla/http
 *
 * @since    1.0
 */
class AcceptHeader extends QualifiedHeader
{
	/**
	 * AcceptHeader constructor.
	 *
	 * @param   string  $header  The 'Accept' header
	 */
	public function __construct($header)
	{
		parent::__construct($header, '/', '*');
	}
}
