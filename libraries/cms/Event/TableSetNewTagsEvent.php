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
 * Event class for JTable's onSetNewTags event
 *
 * TODO This is only used in JModelAdmin::batchTag. We need to remove it but we can't use JTable::save as we don't want the table data to be saved. Maybe trigger the onBeforeStore event instead?
 */
class TableSetNewTagsEvent extends AbstractImmutableEvent
{
	/**
	 * Constructor.
	 *
	 * Mandatory arguments:
	 * subject		JTableInterface	The table we are operating on
	 * newTags 		int[]			New tags to be added to or replace current tags for an item
	 * replaceTags	bool			Replace tags (true) or add them (false)
	 *
	 * @param   string  $name       The event name.
	 * @param   array   $arguments  The event arguments.
	 *
	 * @throws  BadMethodCallException
	 */
	public function __construct($name, array $arguments = array())
	{
		if (!isset($arguments['newTags']))
		{
			throw new BadMethodCallException("Argument 'newTags' is required for event $name");
		}

		if (!isset($arguments['replaceTags']))
		{
			throw new BadMethodCallException("Argument 'replaceTags' is required for event $name");
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

	/**
	 * Setter for the replaceTags attribute
	 *
	 * @param   mixed  $value  The value to set
	 *
	 * @return  boolean  Normalised value
	 */
	protected function setReplaceTags($value)
	{
		return $value ? true : false;
	}
}