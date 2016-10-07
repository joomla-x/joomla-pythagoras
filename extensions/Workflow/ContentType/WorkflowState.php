<?php
/**
 * Part of the Joomla Workflow Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Workflow\ContentType;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;
use Joomla\Content\CustomContentTypeInterface;
use Joomla\Content\Type\AbstractContentType;
use Joomla\Renderer\HtmlRenderer;
use Joomla\Renderer\RendererInterface;
use Joomla\Tests\Unit\DumpTrait;

/**
 * WorkflowState ContentType
 *
 * @package  Joomla/Workflow
 * @since    __DEPLOY_VERSION__
 *
 * @property string                 $type
 * @property ContentTypeInterface[] $elements
 */
class WorkflowState extends AbstractContentType implements CustomContentTypeInterface
{
	/** @var  RendererInterface */
	private $renderer;

	use DumpTrait;

	/**
	 * Constructor.
	 *
	 * @param   object $item The item to be displayed
	 */
	public function __construct($item = null)
	{
		$this->item = $item;
	}

	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 *
	 * @return  mixed
	 */
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		return $visitor->visitWorkflowState($this);
	}

	public function register(RendererInterface $renderer)
	{
		$this->renderer = $renderer;

		switch ($renderer->getClass())
		{
			case HtmlRenderer::class:
				$method = 'asHtml';
				break;

			default:
				$method = 'asPlain';
				break;
		}

		$renderer->registerContentType('WorkflowState', [$this, $method]);
	}

	public function asHtml($content)
	{
		$state = $this->getState($content);

		$this->renderer->write("<pre class=\"workflow-state workflow-{$state}\">State: $state</pre>");
	}

	public function asPlain($content)
	{
		$state = $this->getState($content);

		$this->renderer->write($state);
	}

	/**
	 * @return string
	 */
	private function getState($content)
	{
		$state = $content->item->stateEntities->getAll();

		if (empty($state))
		{
			return '';
		}

		return strtolower($state[0]->state->title);
	}
}
