<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\CommandHandlerBase;

final class SimpleCommandHandler extends CommandHandlerBase
{
	public function handle(SimpleCommand $command)
	{
		return true;
	}
}
