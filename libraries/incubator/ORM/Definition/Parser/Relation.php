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
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
abstract class Relation extends Element
{
	/** @var  string  The relation name */
	public $name = null;

	/** @var  string  The relation type */
	public $type;

	/** @var  Entity  The related Entity */
	public $entity;

	/** @var  string  Key name in related entity */
	public $reference;
}
