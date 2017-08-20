<?php

namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Joomla\Session\Session;

class SessionTest extends \PHPUnit_Framework_TestCase
{

    public function testSetGet()
    {
        $session = new Session([]);

        $this->assertNull($session->get('unit'));

        $session->set('unit', 'test');
        $this->assertEquals('test', $session->get('unit'));
    }
}
