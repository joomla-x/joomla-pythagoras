<?php
/**
 * Part of the Joomla Article Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Category\Entity;

/**
 * Class CategoryEntityMap
 *
 * @package  Joomla\Extension\Category
 *
 * @since    __DEPLOY_VERSION__
 */
class CategoryEntityMap
{
    /** @var  integer  The ID of the category */
    public $categoryId;

    /** @var  string  The type of the related entity */
    public $entityType;

    /** @var  integer  The ID of the related entity */
    public $entityId;
}
