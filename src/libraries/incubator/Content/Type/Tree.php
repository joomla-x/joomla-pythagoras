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
 * Tree ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property object $item
 */
class Tree extends AbstractContentType
{
    /**
     * Tree constructor.
     *
     * @param   object $item The item to be displayed as a tree
     */
    public function __construct($item)
    {
        parent::__construct('Tree', 'tree-' . spl_object_hash($this), new \stdClass);

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
        return $visitor->visitTree($this);
    }
}
