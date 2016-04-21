<?php
use Joomla\DI\ServiceProviderInterface;
use Joomla\DI\Container;

class SimpleServiceProvider implements ServiceProviderInterface
{

	private $value;

	public function __construct ($value = 'called')
	{
		$this->value = $value;
	}

	public function register (Container $container, $alias = null)
	{
		$container->set($alias, $this->value);
	}
}
