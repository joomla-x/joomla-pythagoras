<?php
/**
 * Part of the Joomla CMS Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\System\Page\Joomla3\Hathor;

use Joomla\Tests\Page\Page;

class CPanelPage extends Page
{
    /**
     * @var \AcceptanceTester
     */
    protected $tester;

    /** @var  string */
    protected $url = 'administrator/';

    /**
     * @return boolean
     */
    public function isCurrent()
    {
        return $this->tester->canSeeCurrentUrlEquals('/');
    }
}
