<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class HasManyThrough
 *
 * @package  joomla/orm
 * @since    1.0
 */
class HasManyThrough extends Relation
{
    /** @var  string  Name of the joining table */
    public $joinTable;

    /** @var  string  Name of the field in the joinTable with the remote id */
    public $joinRef;
}
