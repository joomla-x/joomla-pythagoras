<?php

namespace Joomla\Tests\System;

use AcceptanceTester;
use Codeception\Util\Shared\Asserts;
use Joomla\Tests\Page\Joomla3\Installer\InstallerPage;
use Joomla\Tests\Page\PageFactory;

class InstallerCest
{
    use Asserts;

    /** @var  InstallerPage */
    private $page;

    public function _before(AcceptanceTester $I)
    {
        $this->page = (new PageFactory($I, 'Installer'))->create('InstallerPage');

        $I->amOnPage((string)$this->page);
        $I->assertCurrent($this->page);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function InstallationLanguageIsSelectable(AcceptanceTester $I)
    {
        $this->assertNotEquals('en-GB', $this->page->getLanguage());

        $this->page->setLanguage('en-GB');

        $this->assertEquals('en-GB', $this->page->getLanguage());
    }
}
