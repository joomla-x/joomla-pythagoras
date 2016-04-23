<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Finder;

/**
 * Class Operator
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
abstract class Operator
{
	const EQ = '=';
	const EQUAL = '=';
	const NE = '<>';
	const NOT_EQUAL = '<>';
	const GT = '>';
	const GREATER_THAN = '>';
	const GE = '>=';
	const GREATER_OR_EQUAL = '>=';
	const LT = '<';
	const LESS_THAN = '<';
	const LE = '<=';
	const LESS_OR_EQUAL = '<=';
	const CONTAINS = '%LIKE%';
	const STARTS_WITH = 'LIKE%';
	const ENDS_WITH = '%LIKE';
	const MATCHES = 'RLIKE';
	const IN = 'IN';
}
