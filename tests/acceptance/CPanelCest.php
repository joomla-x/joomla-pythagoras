<?php

namespace Joomla\Tests\System;

use AcceptanceTester;
use Codeception\Util\Shared\Asserts;
use Facebook\WebDriver\WebDriver;
use Joomla\Tests\Page\DumpTrait;
use Joomla\Tests\Page\Page;
use Joomla\Tests\Page\PageFactory;

class CPanelCest
{
    use Asserts;
    use DumpTrait;

    /** @var  Page */
    private $page;

    public function _before(AcceptanceTester $I)
    {
        $this->page = (new PageFactory($I, 'Hathor'))->create('CPanelPage');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage((string) $this->page);
        $I->assertCurrent($this->page);

        $this->dumpPage(__METHOD__);
    }
}
