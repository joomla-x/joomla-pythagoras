<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\Type\Accordion;
use Joomla\Content\Type\Article;
use Joomla\Content\Type\Attribution;
use Joomla\Content\Type\Columns;
use Joomla\Content\Type\Compound;
use Joomla\Content\Type\DefaultMenu;
use Joomla\Content\Type\Headline;
use Joomla\Content\Type\Image;
use Joomla\Content\Type\Paragraph;
use Joomla\Content\Type\Rows;
use Joomla\Content\Type\Slider;
use Joomla\Content\Type\Tabs;
use Joomla\Content\Type\Teaser;
use Joomla\Content\Type\Tree;

/**
 * Class PlainRenderer
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
class PlainRenderer extends Renderer
{
    /** @var string The MIME type */
    protected $mediatype = 'text/plain';

    /**
     * Write data to the stream.
     *
     * @param   ContentTypeInterface|string $content The string that is to be written.
     *
     * @return  integer  Returns the number of bytes written to the stream.
     * @throws  \RuntimeException on failure.
     */
    public function write($content)
    {
        if ($content instanceof ContentTypeInterface) {
            $len = $content->accept($this);
        } else {
            echo $content;
            $len = strlen($content);
        }

        return $len;
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
        return $this->write($headline->text . "\n" . str_repeat('=', strlen($headline->text)) . "\n\n");
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
        $len = 0;

        foreach ($compound->elements as $item) {
            $len += $item->content->accept($this);
        }

        return $len;
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
        return $this->write($attribution->label . ' ' . $attribution->text . "\n\n");
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
        return $this->write($paragraph->text . "\n\n");
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
        return $this->write("![{$image->alt}]({$image->url})");
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
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
        return $this->write(print_r($dump->item, true));
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
        return $this->visitCompound($rows);
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
        return $this->visitCompound($columns);
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
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
        throw new \LogicException(__METHOD__ . ' is not implemented.');

        return 0;
    }
}
