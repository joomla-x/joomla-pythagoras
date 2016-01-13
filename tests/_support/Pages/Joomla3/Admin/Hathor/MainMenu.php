<?php
namespace Celtic\Testing\Joomla;

class Joomla3AdminMainMenu extends Menu
{
	protected $levelMap = array(
		array(
			'locator' => 'css selector:nav.navbar #menu',
			'click' => true
		),
		array(
			'locator' => 'xpath:parent::li/ul',
			'click' => false
		),
		array(
			'locator' => 'xpath:following-sibling::ul',
			'click' => false
		),
	);

	protected $pageMap = array(
		'Extension Manager' => array(
			'menu' => 'Extensions/Extension Manager',
			'page' => 'Celtic\\Testing\\Joomla\\Joomla3AdminExtensionManagerInstallPage',
		),
		'Control Panel' => array(
			'menu' => 'System/Control Panel',
			'page' => 'Celtic\\Testing\\Joomla\\Joomla3AdminCPanelPage',
		),
		'default' => array('page' => 'Celtic\\Testing\\Joomla\\Joomla3AdminPage')
	);
}
