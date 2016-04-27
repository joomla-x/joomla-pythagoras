<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;
use League\Tactician\Middleware;

class SimpleMiddleware implements Middleware
{

	public $called = false;

	public function execute ($message, callable $next)
	{
		$this->called = true;
		
		return $next($message);
	}
}
