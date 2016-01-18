<?php

namespace Joomla\Tests\System;

use AcceptanceTester;
use Codeception\Configuration;
use Codeception\Util\Shared\Asserts;
use Facebook\WebDriver\WebDriver;
use Joomla\Tests\Page\DumpTrait;
use Joomla\Tests\Page\Page;
use Joomla\Tests\Page\PageFactory;

class InstallerCest
{
    use Asserts;
    use DumpTrait;

    /** @var  WebDriver */
    private $driver;

    /** @var  Page */
    private $page;

    public function _before(AcceptanceTester $I)
    {
        $this->driver = $I->getWebDriver();
        $this->page = (new PageFactory($I, 'Installer'))->create('InstallerPage');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage((string) $this->page);

        $this->assertTrue($this->page->isCurrent(), 'Expected to be on ' . (string)$this->page . ', but actually on ' . $this->driver->getCurrentURL());

        $I->seeInTitle('Joomla! Web Installer');

        $this->dumpPage(__METHOD__);
    }
}
