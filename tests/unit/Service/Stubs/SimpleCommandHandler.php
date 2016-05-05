<?php
namespace Joomla\Tests\Unit\Service\Stubs;

use Joomla\Service\CommandHandler;

final class SimpleCommandHandler extends CommandHandler
{
	public function handle(SimpleCommand $command)
	{
		return [];
	}
}
