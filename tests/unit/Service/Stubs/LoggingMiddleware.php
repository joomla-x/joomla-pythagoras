<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use League\Tactician\Middleware;

class LoggingMiddleware implements Middleware
{
	private $logger = null;

	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	public function execute($message, callable $next)
	{
		$commandClass = get_class($message);

		$this->logger->log('Starting ' . $commandClass);
		$returnValue = $next($message);
		$this->logger->log('Ending ' . $commandClass);

		return $returnValue;
	}
}
