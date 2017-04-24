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
use Joomla\Content\Type\OnePager;
use Joomla\Content\Type\OnePagerSection;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
use Joomla\Content\Type\Span;
use Joomla\Content\Type\Tabs;
use Joomla\Content\Type\Teaser;
use Joomla\Content\Type\Tree;
use Joomla\ORM\Operator;
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

	/** @var  string[]  CSS code to add to output */
	private $style = [];

	use DumpTrait;

	/**
	 * Render an accordion
	 *
	 * @param   Accordion $accordion The accordion
	 *
	 * @return  void
	 */
	public function visitAccordion(Accordion $accordion)
	{
		$accordion->setId('accordion-' . spl_object_hash($accordion));

		$this->preRenderChildElements($accordion);

		$this->applyLayout('accordion.php', $accordion);
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
			$item->accept($this);
			$item->html = $this->output;
		}

		$this->output = $stash;
	}

	/**
	 * Apply a layout
	 *
	 * @param   string                      $filename The filename of the layout file
	 * @param   object|ContentTypeInterface $content  The content
	 *
	 * @return  void
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

		$this->write($html);
	}

	/**
	 * Render an article
	 *
	 * @param   Article $article The article
	 *
	 * @return  void
	 */
	public function visitArticle(Article $article)
	{
		$this->applyLayout('article.php', $article);
	}

	/**
	 * Render an attribution to an author
	 *
	 * @param   Attribution $attribution The attribution
	 *
	 * @return  void
	 */
	public function visitAttribution(Attribution $attribution)
	{
		$this->applyLayout('attribution.php', $attribution);
	}

	/**
	 * Render columns
	 *
	 * @param   Columns $columns The columns
	 *
	 * @return  void
	 */
	public function visitColumns(Columns $columns)
	{
		$this->preRenderChildElements($columns);

		$this->applyLayout('columns.php', $columns);
	}

	/**
	 * Render a compound (block) element
	 *
	 * @param   Compound $compound The compound
	 *
	 * @return  void
	 */
	public function visitCompound(Compound $compound)
	{
		$id = " id=\"{$compound->getId()}\"";

		$class = $compound->getParameter('class', '');

		if (!empty($class))
		{
			$class = " class=\"$class\"";
		}

		$this->write("<!-- Compound -->\n");
		$this->write("<{$compound->getType()}{$id}{$class}>\n");

		foreach ($compound->elements as $item)
		{
			$item->accept($this);
		}

		$this->write("</{$compound->getType()}>\n");
		$this->write("<!-- /Compound -->\n");
	}

	/**
	 * Render a defaultMenu
	 *
	 * @param   DefaultMenu $defaultMenu The defaultMenu
	 *
	 * @return  void
	 */
	public function visitDefaultMenu(DefaultMenu $defaultMenu)
	{
		$menu              = $this->convertPageTreeToMenu($defaultMenu->item);
		$defaultMenu->item = $menu;

		$this->applyLayout('defaultMenu.php', $defaultMenu);
	}

	/**
	 * @param   Page $page The page
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
	 * @param   string $url  The URL
	 * @param   Page   $page The page
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
	 * Dump an item
	 *
	 * @param   ContentTypeInterface $dump The dump
	 *
	 * @return  void
	 */
	public function visitDump(ContentTypeInterface $dump)
	{
		$this->write('<pre>' . $this->dumpEntity($dump->item) . '</pre>');
	}

	/**
	 * Render a headline.
	 *
	 * @param   Headline $headline The headline
	 *
	 * @return  void
	 */
	public function visitHeadline(Headline $headline)
	{
		$this->applyLayout('headline.php', $headline);
	}

	/**
	 * Render an image
	 *
	 * @param   Image $image The image
	 *
	 * @return  void
	 */
	public function visitImage(Image $image)
	{
		$this->applyLayout('image.php', $image);
	}

	/**
	 * Render a link
	 *
	 * @param Link $link
	 *
	 * @return  void
	 */
	public function visitLink(Link $link)
	{
		$this->applyLayout('link.php', $link);
	}

	/**
	 * Render an OnePager
	 *
	 * @param   OnePager $page The page
	 *
	 * @return  void
	 */
	public function visitOnePager(OnePager $page)
	{
		$this->preRenderChildElements($page);

		$this->applyLayout('onepager.php', $page);
	}

	/**
	 * Render an OnePager section
	 *
	 * @param   OnePagerSection $section The page
	 *
	 * @return  void
	 */
	public function visitOnePagerSection(OnePagerSection $section)
	{
		$this->preRenderChildElements($section);

		$this->applyLayout('onepagerSection.php', $section);
	}

	/**
	 * Render a paragraph
	 *
	 * @param   Paragraph $paragraph The paragraph
	 *
	 * @return  void
	 */
	public function visitParagraph(Paragraph $paragraph)
	{
		$this->applyLayout('paragraph.php', $paragraph);
	}

	/**
	 * Render rows
	 *
	 * @param   Rows $rows The rows
	 *
	 * @return  void
	 */
	public function visitRows(Rows $rows)
	{
		$this->preRenderChildElements($rows);

		$this->applyLayout('rows.php', $rows);
	}

	/**
	 * Render an slider
	 *
	 * @param   Slider $slider The slider
	 *
	 * @return  void
	 */
	public function visitSlider(Slider $slider)
	{
		$slider->setId('slider-' . spl_object_hash($slider));

		$this->preRenderChildElements($slider);

		$this->applyLayout('slider.php', $slider);
	}

	/**
	 * Render a span element
	 *
	 * @param   Span $span The text
	 *
	 * @return  void
	 */
	public function visitSpan(Span $span)
	{
		$this->applyLayout('span.php', $span);
	}

	/**
	 * Render tabs
	 *
	 * @param   Tabs $tabs The tabs
	 *
	 * @return  void
	 */
	public function visitTabs(Tabs $tabs)
	{
		$tabs->setId('tabs-' . spl_object_hash($tabs));

		$this->preRenderChildElements($tabs);

		$this->applyLayout('tabs.php', $tabs);
	}

	/**
	 * Render a teaser
	 *
	 * @param   Teaser $teaser The teaser
	 *
	 * @return  void
	 */
	public function visitTeaser(Teaser $teaser)
	{
		$teaser->url = $this->getFullUrl($teaser->article);

		$this->applyLayout('teaser.php', $teaser);
	}

	/**
	 * @param   object $object The content object
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

	/**
	 * Render a tree
	 *
	 * @param   Tree $tree The tree
	 *
	 * @return  void
	 */
	public function visitTree(Tree $tree)
	{
		$tree->setId('tree-' . spl_object_hash($tree));

		$this->preRenderChildElements($tree);

		$this->applyLayout('tree.php', $tree);
	}

	/**
	 * Render a horizontal line.
	 *
	 * @param   HorizontalLine $headline The horizontal line
	 *
	 * @return  void
	 */
	public function visitHorizontalLine(HorizontalLine $headline)
	{
		$this->write("<hr>\n");
	}

	/**
	 * Render an icon
	 *
	 * @param   Icon $icon The icon
	 *
	 * @return  void
	 */
	public function visitIcon(Icon $icon)
	{
		$this->applyLayout('icon.php', $icon);
	}

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
	 * @param   string $template The template
	 *
	 * @return  void
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * @param   string $label An identifier
	 * @param   string $code  The code associated with that identifier
	 *
	 * @return  void
	 */
	public function addJavascript($label, $code)
	{
		$this->javascript[$label] = $code;
	}

	/**
	 * @param string $namespace
	 * @param string $css
	 *
	 * @return void
	 */
	public function addCss($namespace, $css)
	{
		$this->style[] = preg_replace_callback(
			'~([^{\s]*\s?\{[^{]*?\})~sm',
			function ($match) use ($namespace)
			{
				return "#{$namespace} {$match[0]}";
			},
			$css
		);
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
	 * @return  void
	 */
	public function writeCss()
	{
		$this->write('<style>');
		$this->write(implode("\n", $this->style));
		$this->write('</style>');
	}
}
