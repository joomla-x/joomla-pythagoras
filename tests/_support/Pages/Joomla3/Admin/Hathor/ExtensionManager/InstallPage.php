<?php
namespace Celtic\Testing\Joomla;

use \PHPUnit_Extensions_Selenium2TestCase_Element as Element;

class Joomla3AdminExtensionManagerInstallPage extends Joomla3AdminPage
{
	public function isCurrent()
	{
		return preg_match('~Extension Manager: Install~', $this->headLine()->text());
	}

	/**
	 * Get a tab element
	 *
	 * The installation page has three tabs:
	 * - Upload Package File
	 * - Install from Directory
	 * - Install from URL
	 *
	 * @param   string  $linkText  The label of the tab
	 *
	 * @return  Element
	 */
	protected function tab($linkText)
	{
		return $this->getElement("id:myTabTabs")->byLinkText($linkText);
	}

	/**
	 * Install an extension from a URL
	 *
	 * Navigates to the Installer view and installs the extension.
	 * After installation, the current page is the success view.
	 *
	 * Requires a valid session.
	 *
	 * @param   string  $packageUrl  The URL
	 *
	 * @return  $this
	 */
	public function installFromUrl($packageUrl)
	{
		$this->debug("Installing extension from URL {$packageUrl}.\n");
		$this->tab('Install from URL')->click();

		$urlField = $this->getElement("id:install_url");
		$urlField->clear();
		$urlField->value($packageUrl);
		$this->getElement("xpath://input[contains(@onclick, 'submitbutton4()')]")->click();

		$this->driver->assertContains('success', $this->message()->text(), "Installation from {$packageUrl} failed.");

		return $this;
	}

	/**
	 * Get the output area
	 *
	 * After installation, the output produced by postflight() is rendered in this element.
	 *
	 * @return  Element
	 */
	public function output()
	{
		return $this->getElement("css selector:table.adminform");
	}
}
