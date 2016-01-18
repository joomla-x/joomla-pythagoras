<?php
namespace Celtic\Testing\Joomla;

abstract class AdminPage extends Page
{
	protected $userMenuSelector      = null;
	protected $messageContainer      = null;
	protected $headLineSelector      = null;

	/** @var  Menu */
	public $toolbar;

	public function __construct(AbstractAdapter $driver)
	{
		parent::__construct($driver);
		$this->menu = $driver->pageFactoryCreateFromType('Admin_MainMenu');
		$this->toolbar = $driver->pageFactoryCreateFromType('Admin_Toolbar');
	}

	/**
	 * @return bool
	 */
	public function isCurrent()
	{
		return preg_match(
			'/^administrator\b/',
			str_replace($this->driver->cfg->baseURI, '', $this->driver->url())
		);
	}

	/**
	 * @return Joomla3AdminLoginPage
	 */
	abstract public function logout();

	/**
	 * @return \PHPUnit_Extensions_Selenium2TestCase_Element
	 */
	public function headLine()
	{
		return $this->driver->getElement($this->headLineSelector);
	}

	/**
	 * @return \PHPUnit_Extensions_Selenium2TestCase_Element
	 */
	public function message()
	{
		return $this->driver->getElement($this->messageContainer);
	}
}
