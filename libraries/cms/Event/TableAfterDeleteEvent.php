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
 * Event class for JTable's onAfterDelete event
 */
class TableAfterDeleteEvent extends AbstractImmutableEvent
{
	/**
	 * Constructor.
	 *
	 * Mandatory arguments:
	 * subject		JTableInterface	The table we are operating on
	 * pk			The optional primary key value we deleted.
	 *
	 * @param   string  $name       The event name.
	 * @param   array   $arguments  The event arguments.
	 *
	 * @throws  BadMethodCallException
	 */
	public function __construct($name, array $arguments = array())
	{
		if (!isset($arguments['pk']))
		{
			throw new BadMethodCallException("Argument 'pk' is required for event $name");
		}

		parent::__construct($name, $arguments);
	}

	/**
	 * Setter for the subject argument
	 *
	 * @param   JTableInterface  $value  The value to set
	 *
	 * @return  JTableInterface
	 *
	 * @throws  BadMethodCallException  if the argument is not of the expected type
	 */
	protected function setSubject($value)
	{
		if (!is_object($value) || !($value instanceof JTableInterface))
		{
			throw new BadMethodCallException("Argument 'subject' of event {$this->name} is not of the expected type");
		}

		return $value;
	}
}