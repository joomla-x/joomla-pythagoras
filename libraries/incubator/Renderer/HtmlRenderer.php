<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Cms\Entity\Menu;
use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Article;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\DefaultMenu;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\HorizontalLine;
use Joomla\Content\Type\Icon;
use Joomla\Content\Type\Image;
use Joomla\Content\Type\Link;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
use Joomla\Content\Type\Span;
use Joomla\Content\Type\Tabs;
use Joomla\Content\Type\Teaser;
use Joomla\Content\Type\Tree;
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\PageBuilder\Entity\Layout;
use Joomla\PageBuilder\Entity\Page;
use Joomla\Renderer\Exception\NotFoundException;
use Joomla\Tests\Unit\DumpTrait;

/**
 * Class HtmlRenderer
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
class HtmlRenderer extends Renderer
{
	/** @var string The MIME type */
	protected $mediatype = 'text/html';

	/** @var string  Template directory */
	protected $template;

	/** @var string  Layout directory */
	protected $layoutDirectory = 'bootstrap-3';

	/** @var  ScriptStrategyInterface */
	private $clientScript;

	/** @var  string[]  Javascript code to add to output */
	private $javascript = [];

	use DumpTrait;

	/**
	 * @param   ScriptStrategyInterface $strategy The scripting strategy (library) to use
	 *
	 * @return  void
	 */
	public function setScriptStrategy(ScriptStrategyInterface $strategy)
	{
		$this->clientScript = $strategy;
	}

	/**
	 * Sets the template
	 *
	 * @param   string  $template  The template
	 *
	 * @return  void
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * @param   string  $label  An identifier
	 * @param   string  $code   The code associated with that identifier
	 *
	 * @return  void
	 */
	protected function addJavascript($label, $code)
	{
		$this->javascript[$label] = $code;
	}

	/**
	 * @return  void
	 */
	public function writeJavascript()
	{
		$this->write('<script type="text/javascript">');
		$this->write(implode("\n", $this->javascript));
		$this->write('</script>');
	}

	/**
	 * @return  array
	 */
	protected function collectMetadata()
	{
		$metaData                                  = parent::collectMetadata();
		$metaData['wrapper_data']['client_script'] = empty($this->clientScript) ? null : get_class($this->clientScript);

		return $metaData;
	}

	/**
	 * Apply a layout
	 *
	 * @param   string                      $filename The filename of the layout file
	 * @param   object|ContentTypeInterface $content  The content
	 *
	 * @return  integer
	 */
	private function applyLayout($filename, $content)
	{
		$layout = JPATH_ROOT . '/' . $this->template . '/overrides/' . $filename;

		if (!file_exists($layout))
		{
			$layout = JPATH_ROOT . '/layouts/' . $this->layoutDirectory . '/' . $filename;
		}

		ob_start();
		include $layout;
		$html = ob_get_clean();

		return $this->write($html);
	}

	/**
	 * Render a headline.
	 *
	 * @param   Headline $headline The headline
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitHeadline(Headline $headline)
	{
		return $this->applyLayout('headline.php', $headline);
	}

	/**
	 * Render a horizontal line.
	 *
	 * @param   HorizontalLine $headline The horizontal line
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitHorizontalLine(HorizontalLine $headline)
	{
		return $this->write("<hr>\n");;
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   Attribution $attribution The attribution
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitAttribution(Attribution $attribution)
	{
		return $this->applyLayout('attribution.php', $attribution);
	}

	/**
	 * Render a paragraph
	 *
	 * @param   Paragraph $paragraph The paragraph
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitParagraph(Paragraph $paragraph)
	{
		return $this->applyLayout('paragraph.php', $paragraph);
	}

	/**
	 * Render a span element
	 *
	 * @param   Span $span The text
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitSpan(Span $span)
	{
		return $this->applyLayout('span.php', $span);
	}

	/**
	 * Render a compound (block) element
	 *
	 * @param   Compound $compound The compound
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitCompound(Compound $compound)
	{
		$id = isset($compound->params->id) ? $compound->params->id : '';
		$class = isset($compound->params->class) ? $compound->params->class : '';

		if (!empty($class))
		{
			$id = " id=\"$id\"";
		}

		if (!empty($class))
		{
			$class = " class=\"$class\"";
		}

		$len = 0;
		$len += $this->write("<{$compound->type}{$id}{$class}>\n");

		foreach ($compound->elements as $item)
		{
			$len += $item->content->accept($this);
		}

		$len += $this->write("</{$compound->type}>\n");

		return $len;
	}

	/**
	 * Render an icon
	 *
	 * @param   Icon $icon The icon
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitIcon(Icon $icon)
	{
		return $this->applyLayout('icon.php', $icon);
	}

	/**
	 * Render an image
	 *
	 * @param   Image $image The image
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitImage(Image $image)
	{
		return $this->applyLayout('image.php', $image);
	}

	/**
	 * Render a link
	 *
	 * @param Link $link
	 *
	 * @return int Number of bytes written to the output
	 */
	public function visitLink(Link $link)
	{
		return $this->applyLayout('link.php', $link);
	}

	/**
	 * Render an slider
	 *
	 * @param   Slider $slider The slider
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitSlider(Slider $slider)
	{
		$slider->id = 'slider-' . spl_object_hash($slider);

		$this->preRenderChildElements($slider);

		return $this->applyLayout('slider.php', $slider);
	}

	/**
	 * Render an accordion
	 *
	 * @param   Accordion $accordion The accordion
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitAccordion(Accordion $accordion)
	{
		$accordion->id = 'accordion-' . spl_object_hash($accordion);

		$this->preRenderChildElements($accordion);

		return $this->applyLayout('accordion.php', $accordion);
	}

	/**
	 * Render a tree
	 *
	 * @param   Tree $tree The tree
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitTree(Tree $tree)
	{
		$tree->id = 'tree-' . spl_object_hash($tree);

		$this->preRenderChildElements($tree);

		return $this->applyLayout('tree.php', $tree);
	}

	/**
	 * Render tabs
	 *
	 * @param   Tabs $tabs The tabs
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitTabs(Tabs $tabs)
	{
		$tabs->id = 'tabs-' . spl_object_hash($tabs);

		$this->preRenderChildElements($tabs);

		return $this->applyLayout('tabs.php', $tabs);
	}

	/**
	 * Dump an item
	 *
	 * @param   ContentTypeInterface $dump The dump
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitDump(ContentTypeInterface $dump)
	{
		return $this->write('<pre>' . $this->dumpEntity($dump->item) . '</pre>');
	}

	/**
	 * Render rows
	 *
	 * @param   Rows $rows The rows
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitRows(Rows $rows)
	{
		$this->preRenderChildElements($rows);

		return $this->applyLayout('rows.php', $rows);
	}

	/**
	 * Render columns
	 *
	 * @param   Columns $columns The columns
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitColumns(Columns $columns)
	{
		$this->preRenderChildElements($columns);

		return $this->applyLayout('columns.php', $columns);
	}

	/**
	 * Render an article
	 *
	 * @param   Article $article The article
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitArticle(Article $article)
	{
		return $this->applyLayout('article.php', $article);
	}

	/**
	 * Render a teaser
	 *
	 * @param   Teaser $teaser The teaser
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitTeaser(Teaser $teaser)
	{
		$teaser->url = $this->getFullUrl($teaser->article);

		return $this->applyLayout('teaser.php', $teaser);
	}

	/**
	 * Render a defaultMenu
	 *
	 * @param   DefaultMenu $defaultMenu The defaultMenu
	 *
	 * @return  integer Number of bytes written to the output
	 */
	public function visitDefaultMenu(DefaultMenu $defaultMenu)
	{
		$menu = $this->convertPageTreeToMenu($defaultMenu->item);
		$defaultMenu->item = $menu;

		return $this->applyLayout('defaultMenu.php', $defaultMenu);
	}

	/**
	 * @param   Page  $page  The page
	 *
	 * @return  Menu
	 */
	private function convertPageTreeToMenu($page)
	{
		$menu = new Menu(
			$page->title,
			$this->expandUrl($page->url, $page)
		);

		foreach ($page->children->getAll() as $child)
		{
			$menu->add($this->convertPageTreeToMenu($child));
		}

		return $menu;
	}

	/**
	 * @param   ContentTypeInterface $content The content element
	 *
	 * @return  void
	 */
	private function preRenderChildElements(ContentTypeInterface $content)
	{
		if (!isset($content->elements))
		{
			return;
		}

		$stash = $this->output;

		foreach ($content->elements as $key => $item)
		{
			$this->output = '';
			$item->content->accept($this);
			$content->elements[$key]->html = $this->output;
		}

		$this->output = $stash;
	}

	/**
	 * @param   string  $url   The URL
	 * @param   Page    $page  The page
	 *
	 * @return string
	 */
	private function expandUrl($url, $page)
	{
		if (empty($url))
		{
			return '/index.php';
		}

		while ($url[0] != '/' && !empty($page->parent))
		{
			// @todo refactor
			if ($page->parent instanceof Layout)
			{
				break;
			}

			$page = $page->parent;
			$url  = $page->url . '/' . $url;
		}

		if ($url[0] != '/')
		{
			$url = '/' . $url;
		}

		return '/index.php' . $url;
	}

	/**
	 * @param   object $object  The content object
	 *
	 * @return  string
	 */
	private function getFullUrl($object)
	{
		$repository   = $this->container->get('Repository')->forEntity('Content');
		$entityType   = explode('\\', get_class($object));
		$entityType   = array_pop($entityType);
		$contentItems = $repository->findAll()->with('component', Operator::EQUAL, $entityType)->getItems();

		$candidates = [];

		foreach ($contentItems as $item)
		{
			if (!empty($item->selection) && !empty($item->selection->alias))
			{
				$candidates[] = $this->expandUrl($object->alias, $item->page);
			}
		}

		if (empty($candidates))
		{
			throw new NotFoundException('Unable to find a URL');
		}

		if (count($candidates) > 1)
		{
			// @todo Warn about ambiguosity
		}

		return $candidates[0];
	}
}
