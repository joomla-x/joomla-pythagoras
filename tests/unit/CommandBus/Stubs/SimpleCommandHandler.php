<?php
namespace Joomla\Tests\Unit\CommandBus\Stubs;

use Joomla\CommandBus\CommandHandler;

final class SimpleCommandHandler extends CommandHandler
{
	public function handle(SimpleCommand $command)
	{
		return [];
	}
}
