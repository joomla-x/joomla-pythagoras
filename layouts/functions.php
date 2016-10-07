<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @codingStandardsIgnoreStart
 */

/**
 * @param   mixed $measure The measure
 *
 * @return  string
 */
function marshalMeasure($measure)
{
	if (preg_match('~^\d+$~', $measure))
	{
		$measure .= 'px';
	}

	return $measure;
}
