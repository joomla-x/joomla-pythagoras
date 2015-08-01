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
 * Event class for JTable's onBeforeLoad event
 */
class TableBeforeLoadEvent extends AbstractTableEvent
{
	/**
	 * Constructor.
	 *
	 * Mandatory arguments:
	 * subject	JTableInterface	The table we are operating on
	 * keys		mixed			The optional primary key value to load the row by, or an array of fields to match.
	 * reset	boolean			True to reset the default values before loading the new row.
	 *
	 * @param   string  $name       The event name.
	 * @param   array   $arguments  The event arguments.
	 *
	 * @throws  BadMethodCallException
	 */
	public function __construct($name, array $arguments = array())
	{
		if (!isset($arguments['keys']))
		{
			throw new BadMethodCallException("Argument 'keys' is required for event $name");
		}

		if (!isset($arguments['reset']))
		{
			throw new BadMethodCallException("Argument 'reset' is required for event $name");
		}

		parent::__construct($name, $arguments);
	}

	/**
	 * Setter for the reset attribute
	 *
	 * @param   mixed  $value  The value to set
	 *
	 * @return  boolean  Normalised value
	 */
	protected function setReset($value)
	{
		return $value ? true : false;
	}
}