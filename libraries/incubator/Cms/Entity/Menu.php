<?php
/**
 * Part of the Joomla CMS Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Entity;

/**
 * Class Content
 *
 * @package  Joomla\Cms
 *
 * @since    __DEPLOY_VERSION__
 */
class Menu
{
	public $label;
	public $icon;
	public $link;
	public $children;

	public function __construct($label, $link, $icon = null, $children = [])
	{
		$this->label    = $label;
		$this->link     = $link;
		$this->icon     = $icon;
		$this->children = $children;
	}

	public function add(Menu $child)
	{
		$this->children[] = $child;
	}
}
