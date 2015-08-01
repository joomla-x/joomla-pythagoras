<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Error
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Event;

defined('JPATH_PLATFORM') or die;

use BadMethodCallException;
use JTableInterface;

/**
 * Event class for JTable's onAfterLoad event
 */
class TableAfterLoadEvent extends AbstractTableEvent
{
	/**
	 * Constructor.
	 *
	 * Mandatory arguments:
	 * subject	JTableInterface	The table we are operating on
	 * result	JTableInterface The loaded record
	 * row		null|array		The values loaded from the database, null if it failed
	 *
	 * @param   string $name      The event name.
	 * @param   array  $arguments The event arguments.
	 *
	 * @throws  BadMethodCallException
	 */
	public function __construct($name, array $arguments = array())
	{
		if (!isset($arguments['result']))
		{
			throw new BadMethodCallException("Argument 'result' is required for event $name");
		}

		if (!isset($arguments['row']))
		{
			throw new BadMethodCallException("Argument 'row' is required for event $name");
		}

		parent::__construct($name, $arguments);
	}

	/**
	 * Setter for the result argument
	 *
	 * @param   JTableInterface  $value  The value to set
	 *
	 * @return  JTableInterface
	 *
	 * @throws  BadMethodCallException  if the argument is not of the expected type
	 */
	protected function setResult($value)
	{
		if (!is_object($value) || !($value instanceof JTableInterface))
		{
			throw new BadMethodCallException("Argument 'result' of event {$this->name} is not of the expected type");
		}

		return $value;
	}

	/**
	 * Setter for the row argument
	 *
	 * @param   array|null  $value  The value to set
	 *
	 * @return  array|null
	 *
	 * @throws  BadMethodCallException  if the argument is not of the expected type
	 */
	protected function setRow($value)
	{
		if (!is_null($value) && !is_array($value))
		{
			throw new BadMethodCallException("Argument 'row' of event {$this->name} is not of the expected type");
		}

		return $value;
	}

}