<?php
/**
 * Part of the Joomla PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder\Entity;

use Joomla\ORM\Repository\RepositoryInterface;

/**
 * Class Layout
 *
 * @package  Joomla\PageBuilder\Entity
 *
 * @since    __DEPLOY_VERSION__
 */
class Layout
{
    public $id;
    public $title;

    /** @var  RepositoryInterface */
    public $content;
}
