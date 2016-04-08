<?php

namespace Joomla\Tests\Unit\Renderer\Mock;

use Joomla\Content\ContentTypeInterface;
use Joomla\Renderer\RendererInterface;

abstract class Content implements ContentTypeInterface
{
	protected $content = 'undefined';

	public function __construct($content)
	{
		$this->content = $content;
	}

	public function getContents()
	{
		return $this->content;
	}
}

class ContentType extends Content
{
	public function accept(RendererInterface $renderer)
	{
		$renderer->visitContent($this);
	}
}

class NewContentType extends Content
{
	public function accept(RendererInterface $renderer)
	{
		$renderer->visitNewContent($this);
	}

	public static function asHtml(NewContentType $content)
	{
		return 'static: ' . $content->getContents() . "\n";
	}
}

class OtherContentType extends Content
{
	public function accept(RendererInterface $renderer)
	{
		$renderer->visitOtherContent($this);
	}

	public function asHtml()
	{
		return 'dynamic: ' . $this->getContents() . "\n";
	}
}

class UnregisteredContentType extends Content
{
	public function accept(RendererInterface $renderer)
	{
		$renderer->visitUnregisteredContent($this);
	}
}
