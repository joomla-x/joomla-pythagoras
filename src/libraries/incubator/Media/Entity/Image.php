<?php
/**
 * Part of the Joomla Media Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Media\Entity;

/**
 * Class Image
 *
 * @package  Joomla\Media
 *
 * @since    __DEPLOY_VERSION__
 */
class Image
{
    /** @var  string The ID */
    public $id;

    /** @var  string A caption */
    public $caption;

    /** @var  string The description */
    public $description;

    /** @var  string The URL for the image (src) */
    public $url;

    /** @var  string Name of the creator of the image */
    public $creator;

    /** @var  string The license of the image */
    public $license;

    /** @var  string The Mime type */
    public $mimeType;

    /** @var  integer The width */
    public $width;

    /** @var  integer The height */
    public $height;

    /** @var  string JSON encoded EXIF data */
    public $exif;
}
