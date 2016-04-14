<?php
namespace Joomla\Tests\Unit\Plugins\Content\Uppercase\Listener;
use Joomla\Plugin\Content\UpperCase\Listener\UpperCaseListener;
use Joomla\Content\Type\Paragraph;
use Joomla\Renderer\Event\RenderContentTypeEvent;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Compound;

class UpperCaseListenerTest extends \PHPUnit_Framework_TestCase
{

	public function testUpperCaseListenerParagraph ()
	{
		$listener = new UpperCaseListener();

		$p = new Paragraph('unit test');
		$listener->toUpperCase(new RenderContentTypeEvent('onBeforeRenderParagraph', $p));

		$this->assertEquals('UNIT TEST', $p->text);
	}

	public function testUpperCaseListenerHeadline ()
	{
		$listener = new UpperCaseListener();

		$h = new Headline('unit test');
		$listener->toUpperCase(new RenderContentTypeEvent('onBeforeRenderHeadline', $h));

		$this->assertEquals('UNIT TEST', $h->text);
	}

	public function testUpperCaseListenerCompound ()
	{
		$listener = new UpperCaseListener();

		$c = new Compound('section', []);
		$c->add(new Paragraph('paragraph unit test'));
		$c->add(new Headline('headline unit test'));
		$listener->toUpperCase(new RenderContentTypeEvent('onBeforeRenderCompound', $c));

		$this->assertEquals('PARAGRAPH UNIT TEST', $c->items[0]->text);
		$this->assertEquals('HEADLINE UNIT TEST', $c->items[1]->text);
	}
}
