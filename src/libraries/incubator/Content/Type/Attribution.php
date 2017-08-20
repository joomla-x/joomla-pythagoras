<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\ContentTypeVisitorInterface;

/**
 * Attribution ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property string $label
 * @property string $name
 */
class Attribution extends AbstractContentType
{
    /**
     * Attribution constructor.
     *
     * @param   string $label The text before the author's name
     * @param   string $name  The author's name
     */
    public function __construct($label, $name)
    {
        parent::__construct($label, 'lbl-' . spl_object_hash($this), new \stdClass);

        $this->label = $label;
        $this->name  = $name;
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
        return $visitor->visitAttribution($this);
    }
}
