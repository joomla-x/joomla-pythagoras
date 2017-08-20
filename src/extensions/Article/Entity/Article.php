<?php
/**
 * Part of the Joomla Article Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Article\Entity;

/**
 * Class Article
 *
 * @package  Joomla\Extension\Article
 *
 * @since    __DEPLOY_VERSION__
 */
class Article
{
    /** @var  integer  The ID */
    public $id;

    /** @var  string  The title */
    public $title;

    /** @var  string  A category */
    public $category;

    /** @var  string  The (relative) URL */
    public $alias;

    /** @var  string  The teaser text */
    public $teaser;

    /** @var  string  The image path */
    public $image;

    /** @var  string  The article's copy text */
    public $body;

    /** @var  string  The author's name */
    public $author;

    /** @var  string  The license of the article */
    public $license;
}
