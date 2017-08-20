<?php

namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\Command;

final class ComplexCommand extends Command
{
    protected $arg1 = null;
    protected $arg2 = null;

    public function __construct($arg1 = null, $arg2 = null)
    {
        parent::__construct();

        $this->validate($arg1, $arg2);

        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    private function validate($arg1, $arg2)
    {
        if (is_null($arg1)) {
            throw new \RuntimeException('Argument 1 cannot be null');
        }
    }
}
