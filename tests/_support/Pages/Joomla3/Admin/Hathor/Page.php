<?php
namespace Celtic\Testing\Joomla;

class Joomla3AdminPage extends AdminPage
{
	/** @var Joomla3AdminMainMenu */
	public $menu = null;

	protected $userMenuSelector      = 'css selector:nav.navbar ul.pull-right';
	protected $messageContainer      = "id:system-message-container";
	protected $headLineSelector      = "css selector:h1.page-title";

	public function __construct($driver)
	{
		parent::__construct($driver);
		$this->menu = new Joomla3AdminMainMenu($driver);
		$this->toolbar = new Joomla3AdminToolbar($driver);
	}

	/**
	 * @return Joomla3AdminLoginPage
	 */
	public function logout()
	{
		$userMenu = $this->driver->getElement($this->userMenuSelector);
		$userMenu->byTag('a')->click();

		$userMenu->byLinkText('Logout')->click();

		return new Joomla3AdminLoginPage($this->driver);
	}
}
