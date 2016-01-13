<?php
namespace Celtic\Testing\Joomla;

class Joomla3AdminLoginPage extends Joomla3AdminPage
{
	public function isCurrent()
	{
		$form = $this->driver->byTag('form');
		$id   = $form->attribute('id');

		return $id == 'form-login';
	}

	public function login($username, $password)
	{
		$this->getElement("id:mod-login-username")->value($username);
		$this->getElement("id:mod-login-password")->value($password);
		$this->getElement("xpath://button[contains(., 'Log in')]")->click();

		return $this->driver->pageFactoryCreateFromType('Admin_CPanelPage');
	}
}
