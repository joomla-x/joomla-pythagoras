<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Taggable
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Event\DispatcherInterface;
use Joomla\Cms\Event as CmsEvent;

/**
 * Implements the Taggable behaviour which allows extensions to automatically support tags for their content items.
 *
 * This plugin supersedes JHelperObserverTags.
 *
 * @since   4.0.0
 */
class PlgBehaviourTaggable extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   DispatcherInterface &$subject   The object to observe
	 * @param   array               $config     An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                          (this list is not meant to be comprehensive).
	 *
	 * @since   1.5
	 */
	public function __construct(&$subject, $config = array())
	{
		$this->allowLegacyListeners = false;

		parent::__construct($subject, $config);
	}

	/**
	 * Pre-processor for $table->store($updateNulls)
	 *
	 * @param   CmsEvent\TableBeforeStoreEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onBeforeStore(CmsEvent\TableBeforeStoreEvent $event)
	{
		// Extract arguments
		/** @var JTableInterface $table */
		$table			= $event['subject'];

		// Parse the type alias
		$typeAlias = $this->parseTypeAlias($table);

		// If the table doesn't support UCM we can't use the Taggable behaviour
		if (is_null($typeAlias))
		{
			return;
		}

		// If the table doesn't have a tags helper we can't proceed
		if (!property_exists($table, 'tagsHelper'))
		{
			return;
		}

		// Get the Tags helper and assign the parsed alias
		$tagsHelper = new JHelperTags();
		$tagsHelper->typeAlias = $typeAlias;

		if (empty($table->tagsHelper->tags))
		{
			$tagsHelper->preStoreProcess($table);
		}
		else
		{
			$tagsHelper->preStoreProcess($table, (array) $table->tagsHelper->tags);
		}
	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 *
	 * @param   CmsEvent\TableAfterStoreEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onAfterStore(CmsEvent\TableAfterStoreEvent $event)
	{
		// Extract arguments
		/** @var JTableInterface $table */
		$table			= $event['result'];

		if (!is_object($table) || !($table instanceof JTableInterface))
		{
			return;
		}

		// Parse the type alias
		$typeAlias = $this->parseTypeAlias($table);

		// If the table doesn't support UCM we can't use the Taggable behaviour
		if (is_null($typeAlias))
		{
			return;
		}

		// If the table doesn't have a tags helper we can't proceed
		if (!property_exists($table, 'tagsHelper'))
		{
			return;
		}

		// Get the Tags helper and assign the parsed alias
		$tagsHelper = new JHelperTags();
		$tagsHelper->typeAlias = $typeAlias;

		if (empty($table->tagsHelper->tags))
		{
			$result = $tagsHelper->postStoreProcess($table);
		}
		else
		{
			$result = $tagsHelper->postStoreProcess($table, $table->tagsHelper->tags);
		}
	}

	/**
	 * Pre-processor for $table->delete($pk)
	 *
	 * @param   CmsEvent\TableBeforeDeleteEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onBeforeDelete(CmsEvent\TableBeforeDeleteEvent $event)
	{
		// Extract arguments
		/** @var JTableInterface $table */
		$table			= $event['subject'];
		$pk				= $event['pk'];

		// Parse the type alias
		$typeAlias = $this->parseTypeAlias($table);

		// If the table doesn't support UCM we can't use the Taggable behaviour
		if (is_null($typeAlias))
		{
			return;
		}

		// If the table doesn't have a tags helper we can't proceed
		if (!property_exists($table, 'tagsHelper'))
		{
			return;
		}

		// Get the Tags helper and assign the parsed alias
		$tagsHelper = new JHelperTags();
		$tagsHelper->typeAlias = $typeAlias;

		$this->tagsHelper->deleteTagData($this->table, $pk);
	}

	/**
	 * Handles the tag setting in $table->batchTag($value, $pks, $contexts)
	 *
	 * @param   CmsEvent\TableSetNewTagsEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onSetNewTags(CmsEvent\TableSetNewTagsEvent $event)
	{
		// Extract arguments
		/** @var JTableInterface $table */
		$table			= $event['subject'];
		$newTags		= $event['newTags'];
		$replaceTags	= $event['replaceTags'];

		// Parse the type alias
		$typeAlias = $this->parseTypeAlias($table);

		// If the table doesn't support UCM we can't use the Taggable behaviour
		if (is_null($typeAlias))
		{
			return;
		}

		// If the table doesn't have a tags helper we can't proceed
		if (!property_exists($table, 'tagsHelper'))
		{
			return;
		}

		// Get the Tags helper and assign the parsed alias
		$tagsHelper = new JHelperTags();
		$tagsHelper->typeAlias = $typeAlias;

		if (!$this->tagsHelper->postStoreProcess($table, $newTags, $replaceTags))
		{
			throw new RuntimeException($table->getError());
		}
	}

	/**
	 * Internal method
	 * Parses a TypeAlias of the form "{variableName}.type", replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JTableInterface  $table  The table
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 *
	 * @internal
	 */
	protected function parseTypeAlias(JTableInterface &$table)
	{
		if (!isset($table->typeAlias))
		{
			return null;
		}

		if (empty($table->typeAlias))
		{
			return null;
		}

		return preg_replace_callback('/{([^}]+)}/',
			function($matches) use ($table)
			{
				return $table->{$matches[1]};
			},
			$table->typeAlias
		);
	}
}