<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Di;

// @codingStandardsIgnoreStart

interface StubInterface
{
}

class Stub1 implements StubInterface
{
}

class Stub2 implements StubInterface
{
    public $stub;

    public function __construct(StubInterface $stub)
    {
        $this->stub = $stub;
    }
}

class Stub3
{
    public $stub1;
    public $stub2;

    public function __construct(StubInterface $stub, StubInterface $stub2)
    {
        $this->stub1 = $stub;
        $this->stub2 = $stub2;
    }
}

class Stub4 implements StubInterface
{
}

class Stub5
{
    public $stub;

    public function __construct(Stub4 $stub)
    {
        $this->stub = $stub;
    }
}

class Stub6
{
    public $stub;

    public function __construct($stub = 'foo')
    {
        $this->stub = $stub;
    }
}

class Stub7
{
    public $stub;

    public function __construct($stub)
    {
        $this->stub = $stub;
    }
}

class Stub8
{
    public $stub;

    public function __construct(DoesntExist $stub)
    {
        $this->stub = $stub;
    }
}

class Stub9
{
}
