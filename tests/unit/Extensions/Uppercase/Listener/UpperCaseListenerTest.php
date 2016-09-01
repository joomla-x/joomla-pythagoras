<?php

namespace Joomla\Tests\Unit\Extensions\Uppercase\Listener;

use Joomla\Content\Type\Compound;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Paragraph;
use Joomla\Extension\UpperCase\Listener\UpperCaseListener;
use Joomla\Renderer\Event\RenderContentTypeEvent;

class UpperCaseListenerTest extends \PHPUnit_Framework_TestCase
{
	public function testUpperCaseListenerParagraph()
	{
		$listener = new UpperCaseListener();

		$p = new Paragraph('unit test');
		$listener->toUpperCase(new RenderContentTypeEvent('onBeforeRenderParagraph', $p));

		$this->assertEquals('UNIT TEST', $p->text);
	}

	public function testUpperCaseListenerHeadline()
	{
		$listener = new UpperCaseListener();

		$h = new Headline('unit test');
		$listener->toUpperCase(new RenderContentTypeEvent('onBeforeRenderHeadline', $h));

		$this->assertEquals('UNIT TEST', $h->text);
	}

	public function testUpperCaseListenerCompound()
	{
		$listener = new UpperCaseListener();

		$c = new Compound('section', []);
		$c->add(new Paragraph('paragraph unit test'));
		$c->add(new Headline('headline unit test'));
		$listener->toUpperCase(new RenderContentTypeEvent('onBeforeRenderCompound', $c));

		$this->assertEquals('PARAGRAPH UNIT TEST', $c->elements[0]->text);
		$this->assertEquals('HEADLINE UNIT TEST', $c->elements[1]->text);
	}
}
