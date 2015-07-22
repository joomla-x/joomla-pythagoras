<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Event\DispatcherInterface;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\Registry\Registry;

/**
 * JPlugin Class
 *
 * @since  1.5
 */
abstract class JPlugin implements DispatcherAwareInterface
{
	use DispatcherAwareTrait;

	/**
	 * A Registry object holding the parameters for the plugin
	 *
	 * @var    Registry
	 * @since  1.5
	 */
	public $params = null;

	/**
	 * The name of the plugin
	 *
	 * @var    string
	 * @since  1.5
	 */
	protected $_name = null;

	/**
	 * The plugin type
	 *
	 * @var    string
	 * @since  1.5
	 */
	protected $_type = null;

	/**
	 * The application we are running in
	 *
	 * TODO REFACTOR ME! Use a Container, not the application itself
	 *
	 * @var    JApplicationBase
	 * @since  2.5
	 */
	protected $app = null;

	/**
	 * The database driver we are talking to
	 *
	 * TODO REFACTOR ME! No need for it once we have a Container
	 *
	 * @var    JDatabaseDriver
	 * @since  2.5
	 */
	protected $db = null;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = false;

	/**
	 * Constructor
	 *
	 * @param   DispatcherInterface  &$subject  The object to observe
	 * @param   array                $config    An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                          (this list is not meant to be comprehensive).
	 *
	 * @since   1.5
	 */
	public function __construct(&$subject, $config = array())
	{
		// Get the parameters.
		if (isset($config['params']))
		{
			if ($config['params'] instanceof Registry)
			{
				$this->params = $config['params'];
			}
			else
			{
				$this->params = new Registry;
				$this->params->loadString($config['params']);
			}
		}

		// Get the plugin name.
		if (isset($config['name']))
		{
			$this->_name = $config['name'];
		}

		// Get the plugin type.
		if (isset($config['type']))
		{
			$this->_type = $config['type'];
		}

		// Load the language files if needed.
		if ($this->autoloadLanguage)
		{
			$this->loadLanguage();
		}

		// Ensure there is an application object attached
		if (is_null($this->app))
		{
			$this->app = JFactory::getApplication();
		}

		// Ensure there is a database object attached
		if (is_null($this->db))
		{
			$this->db = JFactory::getDbo();
		}

		// Set the dispatcher we are to register our listeners with
		$this->setDispatcher($subject);

		// Register the event listeners with the dispatcher. Override the registerListeners method to customise.
		$this->registerListeners();
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string  $extension  The extension for which a language file should be loaded
	 * @param   string  $basePath   The basepath to use
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 *
	 * @since   1.5
	 */
	public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if (empty($extension))
		{
			$extension = 'Plg_' . $this->_type . '_' . $this->_name;
		}

		$lang = JFactory::getLanguage();

		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name, null, false, true);
	}

	/**
	 * Registers the Listeners to the Dispatcher.
	 *
	 * By default, this method will look for all public methods whose name starts with "on" and register
	 * them as listeners to an event by the same name. This is pretty much how plugins worked under Joomla!
	 * 1.x, 2.x and 3.x. If you want to customise the Listeners you attach to the Dispatcher you must
	 * override this method.
	 *
	 * @return  void
	 */
	protected function registerListeners()
	{
		$reflectedObject = new ReflectionObject($this);
		$methods = $reflectedObject->getMethods(ReflectionMethod::IS_PUBLIC);

		/** @var ReflectionMethod $method */
		foreach ($methods as $method)
		{
			if (substr($method->name, 0, 2) != 'on')
			{
				continue;
			}

			$this->getDispatcher()->addListener($method->name, [$this, $method->name]);
		}
	}
}
