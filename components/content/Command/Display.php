<?php

namespace Joomla\Component\Content\Command;

class Display extends AbstractCommand
{
	public function execute()
	{
		$this->renderer->write(__METHOD__ . '::input = ' . print_r($this->input, true));
	}
}
