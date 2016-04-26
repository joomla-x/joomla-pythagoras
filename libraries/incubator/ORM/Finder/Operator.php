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
	const EQ = self::EQUAL;
	const EQUAL = '=';
	const NE = self::NOT_EQUAL;
	const NOT_EQUAL = '<>';
	const GT = self::GREATER_THAN;
	const GREATER_THAN = '>';
	const GE = self::GREATER_OR_EQUAL;
	const GREATER_OR_EQUAL = '>=';
	const LT = self::LESS_THAN;
	const LESS_THAN = '<';
	const LE = self::LESS_OR_EQUAL;
	const LESS_OR_EQUAL = '<=';
	const CONTAINS = '%LIKE%';
	const STARTS_WITH = 'LIKE%';
	const ENDS_WITH = '%LIKE';
	const MATCHES = 'RLIKE';
	const IN = 'IN';

	public static $ALL = [
			self::EQUAL,
			self::NOT_EQUAL,
			self::GREATER_THAN,
			self::GREATER_OR_EQUAL,
			self::LESS_THAN,
			self::LESS_OR_EQUAL,
			self::CONTAINS,
			self::STARTS_WITH,
			self::ENDS_WITH,
			self::MATCHES,
			self::IN
	];
}
