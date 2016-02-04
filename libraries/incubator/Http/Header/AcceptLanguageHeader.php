<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Header;

/**
 * Class AcceptLanguageHeader
 *
 * @package joomla/http
 *
 * @since  1.0
 */
class AcceptLanguageHeader extends QualifiedHeader
{
    /**
     * AcceptLanguageHeader constructor.
     *
     * @param $header
     */
    public function __construct($header)
    {
        parent::__construct($header, '-', '');
    }
}
