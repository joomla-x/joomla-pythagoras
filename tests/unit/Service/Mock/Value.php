<?php
/**
 * Part of the Joomla Framework Service Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Service\Mock;

class Value extends \Joomla\Service\Value
{
	public function __construct(array $args)
	{
		foreach ($args as $key => $value)
		{
			$this->{$key} = $value;
		}
		
		parent::__construct();
	}
}
