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
 * This plugin supersedes JTableObserverContenthistory.
 *
 * @since   4.0.0
 */
class PlgBehaviourVersionable extends JPlugin
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
	 * Runs when a new table object is being created
	 *
	 * @param   CmsEvent\Table\ObjectCreateEvent  $event  The event to handle
	 */
	public function onTableObjectCreate(CmsEvent\Table\ObjectCreateEvent $event)
	{

	}

	/**
	 * Pre-processor for $table->store($updateNulls)
	 *
	 * @param   CmsEvent\Table\BeforeStoreEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onTableBeforeStore(CmsEvent\Table\BeforeStoreEvent $event)
	{

	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 *
	 * @param   CmsEvent\Table\AfterStoreEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onTableAfterStore(CmsEvent\Table\AfterStoreEvent $event)
	{

	}

	/**
	 * Pre-processor for $table->delete($pk)
	 *
	 * @param   CmsEvent\Table\BeforeDeleteEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onTableBeforeDelete(CmsEvent\Table\BeforeDeleteEvent $event)
	{

	}

	/**
	 * Handles the tag setting in $table->batchTag($value, $pks, $contexts)
	 *
	 * @param   CmsEvent\Table\SetNewTagsEvent  $event  The event to handle
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onTableSetNewTags(CmsEvent\Table\SetNewTagsEvent $event)
	{

	}

	/**
	 * Runs when an existing table object is reset
	 *
	 * @param   CmsEvent\Table\AfterResetEvent  $event  The event to handle
	 */
	public function onTableAfterReset(CmsEvent\Table\AfterResetEvent $event)
	{

	}


	/**
	 * Runs when an existing table object is reset
	 *
	 * @param   CmsEvent\Table\AfterLoadEvent  $event  The event to handle
	 */
	public function onTableAfterLoad(CmsEvent\Table\AfterLoadEvent $event)
	{

	}
}