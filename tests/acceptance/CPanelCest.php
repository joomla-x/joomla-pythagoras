<?php

namespace Joomla\Tests\System;

use AcceptanceTester;
use Joomla\Tests\Page\Page;
use Joomla\Tests\Page\PageFactory;

class CPanelCest
{
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
        $I->amOnPage($this->page);
    }
}
