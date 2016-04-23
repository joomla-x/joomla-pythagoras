<?php
namespace Joomla\Tests\Unit\Service\Stubs;

class Logger
{
	public function log($info)
	{
		echo 'LOG: ' . $info . "\n";
	}
}
