<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Mocks;

class Article
{
	public $id;
	public $title;
	public $teaser;
	public $body;
	public $author;
	public $license;
	public $parentId = 0;
}
