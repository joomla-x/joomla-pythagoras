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
	/** @var  string The label */
	public $label;

	/** @var  string The icon */
	public $icon;

	/** @var  string The link */
	public $link;

	/** @var  Menu[] The child menu entries */
	public $children;

	/**
	 * Menu constructor.
	 *
	 * @param   string $label    The label
	 * @param   string $link     The link
	 * @param   string $icon     The icon
	 * @param   Menu[] $children The child menu entries
	 */
	public function __construct($label, $link, $icon = null, $children = [])
	{
		$this->label    = $label;
		$this->link     = $link;
		$this->icon     = $icon;
		$this->children = $children;
	}

	/**
	 * Adds a child menu entry
	 *
	 * @param   Menu $child  A child menu entry
	 *
	 * @return  void
	 */
	public function add(Menu $child)
	{
		$this->children[] = $child;
	}
}
