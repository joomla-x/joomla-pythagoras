<?php
/**
 * Part of the Joomla Article Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Category\Entity;

/**
 * Class Category
 *
 * @package  Joomla\Extension\Category
 *
 * @since    __DEPLOY_VERSION__
 */
class Category
{
	/** @var  integer  The ID */
	public $id;

	/** @var  string  The title */
	public $title;

	/** @var  string  The article's copy text */
	public $body;

	/**
	 * Returns the string representation of this entity.
	 * This might be needed if added to entities, that already use a literal category value.
	 *
	 * @return   string
	 */
	public function __toString()
	{
		return $this->title;
	}
}
