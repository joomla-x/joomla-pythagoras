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

    /** @var  Page */
    private $page;

    public function _before(AcceptanceTester $I)
    {
        $this->page = (new PageFactory($I, 'Installer'))->create('InstallerPage');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage((string) $this->page);
        $I->assertCurrent($this->page);

        $I->seeInTitle('Joomla! Web Installer');

        $this->dumpPage(__METHOD__);
    }
}
