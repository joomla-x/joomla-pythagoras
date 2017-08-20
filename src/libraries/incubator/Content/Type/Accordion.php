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
 * Accordion ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property ContentTypeInterface[] $elements
 */
class Accordion extends AbstractCompoundType
{
    /**
     * Visits the content type.
     *
     * @param   ContentTypeVisitorInterface $visitor The Visitor
     *
     * @return  mixed
     */
    public function accept(ContentTypeVisitorInterface $visitor)
    {
        return $visitor->visitAccordion($this);
    }
}
