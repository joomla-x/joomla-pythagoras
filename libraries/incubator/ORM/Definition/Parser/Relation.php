<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Relation
 *
 * @package  Joomla/orm
 * @since    1.0
 */
class Relation extends Element
{
	/** @var  string  The relation name */
	public $name = null;

	/** @var  string  The relation type */
	public $type = 'belongsTo';

	/** @var  Entity  The related Entity */
	public $entity;

	/** @var  string  Key name in related entity */
	public $reference;
}
