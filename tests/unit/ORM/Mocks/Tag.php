<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Mocks;

class Tag
{
	public $id;
	public $tag;
	public $description;

	public function __construct($tag = '', $description = '')
	{
		$this->tag         = $tag;
		$this->description = $description;
	}
}
