<?php

namespace Joomla\Tests\Unit\Renderer\Mock;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;

abstract class Content implements ContentTypeInterface
{
	protected $content = 'undefined content';
	protected $title   = 'undefined title';

	public function __construct($content)
	{
		$this->content = $content;
	}

	public function getContents()
	{
		return $this->content;
	}

	/**
	 * Gets the title for the content
	 *
	 * @return  string
	 */
	public function getTitle()
	{
		return $this->title;
	}
}

class ContentType extends Content
{
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		$visitor->visitContent($this);
	}
}

class NewContentType extends Content
{
	public static function asHtml(NewContentType $content)
	{
		return 'static: ' . $content->getContents() . "\n";
	}

	public function accept(ContentTypeVisitorInterface $visitor)
	{
		$visitor->visitNewContent($this);
	}
}

class OtherContentType extends Content
{
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		$visitor->visitOtherContent($this);
	}

	public function asHtml()
	{
		return 'dynamic: ' . $this->getContents() . "\n";
	}
}

class UnregisteredContentType extends Content
{
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		$visitor->visitUnregisteredContent($this);
	}
}
