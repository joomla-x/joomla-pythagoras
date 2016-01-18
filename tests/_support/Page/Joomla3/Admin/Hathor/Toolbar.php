<?php
namespace Celtic\Testing\Joomla;

class Joomla3AdminToolbar extends Toolbar
{
	protected $pageMap = array(
		'default' => array('page' => 'Celtic\\Testing\\Joomla\\Joomla3AdminPage')
	);

	protected $itemFormat = "xpath://div[@id='toolbar']//button[contains(., '%s')]";
}
