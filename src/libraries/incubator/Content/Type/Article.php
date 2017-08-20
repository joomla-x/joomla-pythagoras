<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;

/**
 * Article ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property \Joomla\Extension\Article\Entity\Article $article
 */
class Article extends AbstractContentType
{
    /**
     * Article constructor.
     *
     * @param   object  $item  The article
     */
    public function __construct($item)
    {
        parent::__construct($item->title, $item->alias, new \stdClass);

        $this->article = $item;
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
        return $visitor->visitArticle($this);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->article->title;
    }
}
