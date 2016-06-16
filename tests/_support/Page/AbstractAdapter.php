<?php
/**
 * Part of the Joomla CMS Test Environment
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Page;

class AbstractAdapter extends PHPUnit_Extensions_Selenium2TestCase
{
	/** @var \SeleniumConfig */
	public $cfg;

	protected $captureScreenshotOnFailure = false;

	protected $screenshotPath = null;

	protected $screenshotUrl = null;

	protected $coverageScriptUrl = null;

	/** @var string The class prefix for this family */
	protected $classPrefix = 'Abstract';

	/**
	 * Constructor
	 *
	 * @param   string $name
	 * @param   array  $data
	 * @param   string $dataName
	 * @param   array  $browser
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function __construct($name = null, array $data = array(), $dataName = '', array $browser = array())
	{
		parent::__construct($name, $data, $dataName, $browser);
	}

	/**
	 * setUp method that is called after the session has been prepared.
	 * It is possible to use session-specific commands like url() here.
	 */
	public function setUpPage()
	{
		/** @var \PHPUnit_Extensions_Selenium2TestCase_Window $window */
		$window = $this->currentWindow();
		$window->position(array('x' => 0, 'y' => 0));
		if (empty($this->cfg->windowSize))
		{
			$window->maximize();
		}
		else
		{
			$window->size($this->cfg->windowSize);
		}
	}

	public function onNotSuccessfulTest(\Exception $e)
	{
		if ($this->captureScreenshotOnFailure)
		{
			$filename = $this->classPrefix . '__' . $this->getTestId() . '__' . date('Y-m-d\TH-i-s');
			file_put_contents(
				$this->screenshotPath . '/' . $filename . '.png',
				$this->currentScreenshot()
			);
			file_put_contents(
				$this->screenshotPath . '/' . $filename . '.html',
				$this->source()
			);

			$e = new \PHPUnit_Extensions_Selenium2TestCase_Exception(
				$e->getMessage() . "\nScreenshot name is $filename\n",
				$e->getCode(),
				$e
			);
		}
		parent::onNotSuccessfulTest($e);
	}

	/**
	 * @param string $locator The locator method:value
	 * @param int    $timeout Max time to wait (in milliseconds)
	 *
	 * @return \PHPUnit_Extensions_Selenium2TestCase_Element
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function getElement($locator, $timeout = null)
	{
		$this->debug("Locating $locator\n");
		$timeout = max(10000, (int) $timeout);
		$driver  = $this;
		list($method, $value) = explode(':', $locator, 2);

		$callback = function () use ($driver, $method, $value)
		{
			try
			{
				$element = $driver->by($method, $value);
				$driver->debug("Got element $method:$value (" . $element->getId() . ")\n");

				return $element;
			}
			catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e)
			{
				$driver->debug("Waiting for $method:$value to appear\n");

				return null;
			}
		};
		$element  = $this->waitUntil($callback, $timeout);
		if (!is_object($element))
		{
			$this->debug("Element is " . var_export($element, true) . "\n");
			$seconds = $timeout / 1000;
			throw new \PHPUnit_Framework_AssertionFailedError("Timed out after $seconds seconds");
		}

		return $element;
	}

	public function debug($text)
	{
		if (isset($this->cfg->debug) && $this->cfg->debug)
		{
			echo($text);
		}
	}

	/**
	 * Locate an element using the given strategy
	 *
	 * Valid strategies are 'class name', 'css selector', 'id', 'link text', 'name', 'tag name', and 'xpath'.
	 *
	 * @param string $method supported by JsonWireProtocol element/ command
	 * @param string $selector
	 *
	 * @return  \PHPUnit_Extensions_Selenium2TestCase_Element
	 */
	public function by($method, $selector)
	{
		return $this->element($this->using($method)->value($selector));
	}

	public function gotoSite()
	{
		$this->debug("Browsing to front end.\n");
		$this->url('');
	}

	protected function setUp()
	{
		$this->cfg = new \SeleniumConfig();

		$this->setHost($this->cfg->host);
		$this->setPort($this->cfg->port);
		$this->setBrowser($this->cfg->browser);
		$this->setBrowserUrl($this->cfg->url);

		$this->debug("\nStarting " . $this->getTestId() . ".\n");

		$this->captureScreenshotOnFailure = (bool) $this->cfg->captureScreenshotOnFailure;
		if ($this->captureScreenshotOnFailure)
		{
			$this->debug("Enabling screenshots.\n");
			$this->screenshotPath = $this->cfg->screenshotPath;
			$this->screenshotUrl  = $this->cfg->screenshotUrl;
		}

		if (!empty($this->cfg->coverageScriptUrl))
		{
			$this->debug("Enabling coverage gathering.\n");
			$this->coverageScriptUrl = $this->cfg->coverageScriptUrl;
		}
	}

	protected function tearDown()
	{
		$this->debug("Finished " . $this->getTestId() . ".\n\n");
	}

	/**
	 * Login into the backend
	 *
	 * Starts a backend session using the provided credentials. If credentials are omitted, the
	 * values for the superadministrator from the server configuration are used.
	 *
	 * If the current page is not the Login view, it will navigate to the backend and logout
	 * the current user before performing the login.
	 *
	 * After the login, the current page is the CPanel view.
	 *
	 * @param   string $username Optional username. If omitted, the SeleniumConfig value is used.
	 * @param   string $password Optional password. If omitted, the SeleniumConfig value is used.
	 *
	 * @return  Joomla3AdminCPanelPage
	 */
	protected function loginToBackend($username = null, $password = null)
	{
		if (!isset($username))
		{
			$username = $this->cfg->username;
		}
		if (!isset($password))
		{
			$password = $this->cfg->password;
		}
		$this->gotoAdmin();

		/** @var \PHPUnit_Extensions_Selenium2TestCase_Session_Cookie $cookies */
		$cookies = $this->cookie();
		$cookies->remove('PHPUNIT_SELENIUM_TEST_ID');
		$cookies->add('PHPUNIT_SELENIUM_TEST_ID', $this->getTestId())->set();

		/** @var Joomla3AdminLoginPage $loginPage */
		$loginPage = $this->pageFactoryCreateFromType('Admin_LoginPage');
		if (!$loginPage->isCurrent())
		{
			$loginPage->logout();
		}
		$this->debug("Logging in to back end.\n");
		$page = $loginPage->login($username, $password);

		$this->assertEquals($this->getTestId(), $cookies->get('PHPUNIT_SELENIUM_TEST_ID'));

		return $page;
	}

	public function gotoAdmin()
	{
		$this->debug("Browsing to back end.\n");
		$this->url('administrator');
	}

	/**
	 * @param $pageType
	 *
	 * @return Page
	 */
	public function pageFactoryCreateFromType($pageType)
	{
		return $this->pageFactoryCreate($this->getPrefix() . '_' . $pageType);
	}

	/**
	 * @param $pageClass
	 *
	 * @return Page
	 */
	public function pageFactoryCreate($pageClass)
	{
		$page = $this->pageFactoryExtendMenu(new $pageClass($this));
		$this->debug("Created " . str_replace($this->getPrefix() . '_', '', get_class($page)) . "\n");

		return $page;
	}

	/**
	 * @param $pageObject
	 *
	 * @return Page
	 */
	public function pageFactoryExtendMenu($pageObject)
	{
		if (isset($this->menuMapExtension) && !empty($pageObject->menu))
		{
			/** @var Menu $pageObject ->menu */
			foreach ($this->menuMapExtension as $label => $page)
			{
				$pageObject->menu->add($label, $page);
			}
		}

		return $pageObject;
	}

	/**
	 * @return string
	 */
	private function getPrefix()
	{
		return __NAMESPACE__ . '\\' . $this->classPrefix;
	}
}
