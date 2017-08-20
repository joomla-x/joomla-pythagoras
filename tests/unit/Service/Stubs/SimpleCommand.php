<?php

namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\Command;

/**
 * Class SimpleCommand
 *
 * @package Joomla\Tests\Unit\Service\Stubs
 *
 * @method string getName()
 * @method string getTest()
 */
final class SimpleCommand extends Command
{
    public function __construct($test = null)
    {
        $this->test = $test;

        parent::__construct();
    }
}
