<?php
namespace Celtic\Testing\Joomla;

/**
 * Class PageDecorator
 *
 * @package Celtic\Testing\Joomla
 * @property Menu $menu
 * @property Menu $toolbar
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element headLine()
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element message()
 */
abstract class PageDecorator extends Page
{
	/** @var  Page */
	protected $page;

	public function __construct(Page $page)
	{
		$this->page = $page;
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->page, $method), $args);
	}

	public function __get($property)
	{
		return $this->page->$property;
	}
}
